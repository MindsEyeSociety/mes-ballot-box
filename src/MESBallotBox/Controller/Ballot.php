<?php namespace MESBallotBox\Controller;

class Ballot{
	function route($slim){
		$slim->post('', function($request, $response){
			$vars = $request->getParsedBody();

			$ballot = new \MESBallotBox\Propel\Ballot();
			$ballot->setName($vars['name']);
			$ballot->setTimezone($vars['timezone']);
			if($vars['startFormat'] && $vars['endFormat']){
				$ballot->setStartDate($vars['startFormat']);
				$ballot->setEndDate($vars['endFormat']);
			}
			else{
				$ballot->setStartTime($vars['start']);
				$ballot->setEndTime($vars['end']);
			}
			$ballot->setUserId($_ENV['ballot_user']['id']);
			$ballot->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$ballot->validate()){
				return $response->withStatus(400)->write($ballot->getValidationFailures()->__toString());
			}
			try{
				$ballot->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($ballot->toJSON());
		});
		$slim->get('', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\BallotQuery();
			$ballots = $q->filterByUserId($_ENV['ballot_user']['id'])->find();
			$results = Array();
			foreach($ballots as $ballot){
				$result = Array();
				$result['id'] = $ballot->getId();
				$result['name'] = $ballot->getName();
				$result['start'] = $ballot->getStartTime();
				$result['end'] = $ballot->getEndTime();
				$result['timezone'] = $ballot->getTimezoneNice();
				$results[] = $result;
			}
			
			return $response->write(json_encode($results));
		});
		$slim->get('/user/{membershipNumber}', function($request, $response, $args){
			$user = \MESBallotBox\Controller\Ballot::getUser($args['membershipNumber']);
			$response->write(json_encode($user));
		});
		$slim->get('/available', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\BallotQuery();
			$time = time();
			$ballots = $q::create()
						->join('Ballot.Voter')
						->condition('byUser', 'Voter.userId = ?', $_ENV['ballot_user']['id'])
						->condition('byAffiliate', 'Voter.affiliateId = ?', $_ENV['ballot_user']['affiliateId'])
						->where(Array('byUser', 'byAffiliate'), 'or')
						->where('Ballot.startTime < ?', $time)
						->where('Ballot.endTime > ?', $time)
						->groupBy('Ballot.id')
						->find();
			$results = Array();
			foreach($ballots as $ballot){
				$result = Array();
				$result['id'] = $ballot->getId();
				$result['name'] = $ballot->getName();
				$result['start'] = $ballot->getStartTime();
				$result['end'] = $ballot->getEndTime();
				$result['timezoneNice'] = $ballot->getTimezoneNice();
				$results[] = $result;
			}
			
			return $response->write(json_encode($results));
		});
		$slim->get('/{ballotId}', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\BallotQuery();
			$ballot = $q->findPK($args['ballotId']);
			$result = Array();
			$result['id'] = $ballot->getId();
			$result['name'] = $ballot->getName();
			$result['start'] = $ballot->getStartTime();
			$result['end'] = $ballot->getEndTime();
			$result['timezone'] = $ballot->getTimezone();
			$result['timezoneNice'] = $ballot->getTimezoneNice();
			$result['startNice'] = $ballot->getStartDate('F jS Y h:i A');
			$result['startFormat'] = $ballot->getStartDate('Y-m-d\TH:i');
			$result['endNice'] = $ballot->getEndDate('F jS Y h:i A');
			$result['endFormat'] = $ballot->getEndDate('Y-m-d\TH:i');
			$result['questions'] = \MESBallotBox\Controller\Ballot::getQuestions($ballot);
			return $response->write(json_encode($result));
		});
		$slim->get('/{ballotId}/voteinfo', function($request, $response, $args){
			$ballot = \MESBallotBox\Controller\Ballot::getVoterBallot($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not available for voting.');
			}
			$result = Array();
			$result['id'] = $ballot->getId();
			$result['name'] = $ballot->getName();
			$result['start'] = $ballot->getStartTime();
			$result['end'] = $ballot->getEndTime();
			$result['timezone'] = $ballot->getTimezone();
			$result['timezoneNice'] = $ballot->getTimezoneNice();
			$result['questions'] = \MESBallotBox\Controller\Ballot::getQuestions($ballot);
			
			$vote = \MESBallotBox\Propel\VoteQuery::create()->filterByBallotId($ballot->getId())->filterbyUserId($_ENV['ballot_user']['id'])->findOne();
			if($vote) $result['voteId'] = $vote->getId();
			else{
				$invalid_message = \MESBallotBox\Controller\Ballot::checkInvalidVoter($ballot);
				if($invalid_message) $result['invalid'] = $invalid_message;
			}
			return $response->write(json_encode($result));
		});
		$slim->post('/{ballotId}', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\BallotQuery();
			$ballot = $q->findPK($args['ballotId']);
			$vars = $request->getParsedBody();
			$ballot->setName($vars['name']);
			$ballot->setTimezone($vars['timezone']);
			if($vars['startFormat'] && $vars['endFormat']){
				$ballot->setStartDate($vars['startFormat']);
				$ballot->setEndDate($vars['endFormat']);
			}
			else{
				$ballot->setStartTime($vars['start']);
				$ballot->setEndTime($vars['end']);
			}
			$ballot->setVersionCreatedBy($_ENV['ballot_user']['id']);

			if(!$ballot->validate()){
				return $response->withStatus(400)->write($ballot->getValidationFailures()->__toString());
			}
			try{
				$ballot->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($ballot->toJSON());
		});
		$slim->get('/{ballotId}/voter', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\VoterQuery();
			$voters = $q->filterByBallotId($args['ballotId'])->find();
			$results = Array();
			foreach($voters as $voter){
				if($voter->getUserId()) $results[] = array_merge($voter->getUser()->toArray(), $voter->toArray());
				else{
					$result = $voter->toArray();
					$orgUnit = \MESBallotBox\Controller\Hub::getOrgUnit($voter->getOrgUnitId());
					$result['name'] = $orgUnit['unit']['name'];
					$result['code'] = $orgUnit['unit']['code'];
					$results[] = $result;
				}
			}
			return $response->write(json_encode($results));
		});
		$slim->post('/{ballotId}/voter', function($request, $response){
			$vars = $request->getParsedBody();
			$q = new \MESBallotBox\Propel\VoterQuery();
			$voter = new \MESBallotBox\Propel\Voter();
			$voter->setBallotId($vars['ballotId']);
			if($vars['membershipNumber'] && $vars['affiliateId']){
				return $response->withStatus(400)->write('Fill in EITHER Membership Number OR Affiliate');
			}
			if($vars['membershipNumber']){
				try{
					$user = \MESBallotBox\Controller\Ballot::getUser($vars['membershipNumber']);
				}catch(\Exception $e){
					return $response->withStatus(500)->write($e->getMessage());
				}
				/*if(!is_array($user) && is_object($user) && method_exists('toArray',$user)) $userReturn = $user->toArray();
				else $userReturn = $user;
				return $response->write(json_encode($userReturn));*/
				if(!$user) return $response->withStatus(400)->write('User not found');
				$existingVoter = $q->filterByBallotId($vars['ballotId'])->filterByUserId($user->getId())->findOne();
				if($existingVoter) return $response->withStatus(400)->write('Already Added');
				$voter->setUserId($user->getId());
			}
			elseif ($vars['orgUnitId']) {
				$existingVoter = $q->filterByBallotId($vars['ballotId'])->filterByOrgUnitId($vars['orgUnitId'])->findOne();
				if($existingVoter) return $response->withStatus(400)->write('Already Added');
				$voter->setOrgUnitId($vars['orgUnitId']);
			}
			else{
				return $response->withStatus(400)->write('Either Organization or member is required');
			}
			
			$voter->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$voter->validate()){
				return $response->withStatus(400)->write($voter->getValidationFailures()->__toString());
			}
			try{
				$voter->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($voter->toJSON());
		});
		$slim->get('/{ballotId}/question', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$q->filterByBallotId($args['ballotId']);
			$params = $request->getQueryParams();
			if(!$params['show_deleted'] || $params['show_deleted'] == 'false') $q->filterByIsDeleted(0);
			$questions = $q->orderByorderId()->find();
			$results = Array();
			foreach($questions as $question){
				$results[] = $question->toArray();
			}
			
			return $response->write(json_encode($results));
		});
		$slim->get('/{ballotId}/question/{questionId}', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$question = $q->findPK($args['questionId']);
			
			return $response->write($question->toJSON());
		});
		$slim->post('/{ballotId}/question', function($request, $response){
			$vars = $request->getParsedBody();

			$question = new \MESBallotBox\Propel\Question();
			$question->fromArray($vars);
			$question->setVersionCreatedBy($_ENV['ballot_user']['id']);
			$max_question = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($question->getBallotId())->orderByorderId('desc')->findOne();
			if(!$max_question) $question->setOrderId(1);
			else $question->setOrderId($max_question->getOrderId()+1);
			if(!$question->validate()){
				return $response->withStatus(400)->write($question->getValidationFailures()->__toString());
			}
			try{
				$question->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($question->toJSON());
		});
		$slim->post('/{ballotId}/question/reorder', function($request, $response, $args){
			$ballot = \MESBallotBox\Propel\BallotQuery::create()->findPK($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not found.');
			}
			$vars = $request->getParsedBody();
			foreach($vars['questions'] as $var_question){
				$question = \MESBallotBox\Propel\QuestionQuery::create()->findPK($var_question['id']);
				if(!$question || $question->getBallotId() != $ballot->getId()){
					return $response->withStatus(400)->write('Question not found.');
				}
				$question->setOrderId($var_question['orderId']);
				$question->setVersionCreatedBy($_ENV['ballot_user']['id']);
				try{
					$question->save();
				}catch(Exception $e){
					return $response->withStatus(500)->write($e->getMessage());
				}
			}
			$questions = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($args['ballotId'])->orderByorderId()->find();
			$results = Array();
			foreach($questions as $question){
				$results[] = $question->toArray();
			}
			
			return $response->write(json_encode($results));
		});
		$slim->post('/{ballotId}/question/{questionId}', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$question = $q->findPK($args['questionId']);
			$vars = $request->getParsedBody();
			
			$question->fromArray($vars);
			$question->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$question->validate()){
				return $response->withStatus(400)->write($question->getValidationFailures()->__toString());
			}
			try{
				$question->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($question->toJSON());
		});
		$slim->delete('/{ballotId}/question/{questionId}', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$question = $q->findPK($args['questionId']);
			
			$question->SetIsDeleted(1);
			$question->SetOrderId(1000);
			$question->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$question->validate()){
				return $response->withStatus(400)->write($question->getValidationFailures()->__toString());
			}
			try{
				$question->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write(json_encode($question->toArray()));
		});
		$slim->post('/{ballotId}/question/{questionId}/restore', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$question = $q->findPK($args['questionId']);
			
			$question->SetIsDeleted(0);
			$max_question = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($question->getBallotId())->filterByIsDeleted(0)->orderByorderId('desc')->findOne();
			if(!$max_question) $question->setOrderId(1);
			else $question->setOrderId($max_question->getOrderId()+1);
			$question->setVersionCreatedBy($_ENV['ballot_user']['id']);
			
			if(!$question->validate()){
				return $response->withStatus(400)->write($question->getValidationFailures()->__toString());
			}
			try{
				$question->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write(json_encode($question->toArray()));
		});
		$slim->post('/{ballotId}/question/{questionId}/candidate', function($request, $response, $args){
			$vars = $request->getParsedBody();
			$q = new \MESBallotBox\Propel\QuestionQuery();
			$question = $q->findPK($vars['questionId']);
			$user = \MESBallotBox\Controller\Ballot::getUser($vars['membershipNumber']);
			if(!$user) return $response->withStatus(400)->write('User not found');
			
			$candidate = new \MESBallotBox\Propel\Candidate();
			$candidate->setQuestionId($question->getId());
			$candidate->setUserId($user->getId());
			$candidate->setApplication($vars['application']);
			$candidate->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$candidate->validate()){
				return $response->withStatus(400)->write($candidate->getValidationFailures()->__toString());
			}
			try{
				$candidate->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($candidate->toJSON());
		});
		$slim->post('/{ballotId}/question/{questionId}/candidate/{candidateId}', function($request, $response, $args){
			$vars = $request->getParsedBody();
			$q = new \MESBallotBox\Propel\CandidateQuery();
			$candidate = $q->findPK($args['candidateId']);
			if(!$candidate) return $response->withStatus(400)->write('Candidate not found');
			$candidate->setApplication($vars['application']);
			$candidate->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$candidate->validate()){
				return $response->withStatus(400)->write($candidate->getValidationFailures()->__toString());
			}
			try{
				$candidate->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			return $response->write($candidate->toJSON());
		});
		$slim->get('/{ballotId}/question/{questionId}/candidate', function($request, $response, $args){
			$q = new \MESBallotBox\Propel\CandidateQuery();
			$candidates = $q->filterByQuestionId($args['questionId'])->find();
			$results = Array();
			foreach($candidates as $candidate){
				$results[] = array_merge($candidate->getUser()->toArray(), $candidate->toArray());
			}
			return $response->write(json_encode($results));
		});
		$slim->post('/{ballotId}/vote', function($request, $response,$args){
			$vars = $request->getParsedBody();
			$ballot = \MESBallotBox\Controller\Ballot::getVoterBallot($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not available for voting.');
			}
			$invalid_message = \MESBallotBox\Controller\Ballot::checkInvalidVoter($ballot);
			if($invalid_message){
				return $response->withStatus(400)->write($invalid_message);
			}
			$vote = new \MESBallotBox\Propel\Vote();
			$vote->setBallotId($ballot->getId());
			$vote->setUserId($_ENV['ballot_user']['id']);
			$vote->setVersionCreatedBy($_ENV['ballot_user']['id']);
			if(!$vote->validate()){
				return $response->withStatus(400)->write($vote->getValidationFailures()->__toString());
			}
			
			if(!$vars['voteItem']){
				return $response->withStatus(400)->write('Vote answers required');
			}
			$voteItems = Array();
			foreach($vars['voteItem'] as $vars_voteItem){
				if(!$vars_voteItem['questionId']) return $response->withStatus(400)->write('Vote question required');
				$question = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($ballot->getId())->filterById($vars_voteItem['questionId'])->findOne();
				if(!$question) return $response->withStatus(400)->write('Question invalid');
				
				if($question->getType() == 'proposition'){
					$voteItem = new \MESBallotBox\Propel\VoteItem();
					$voteItem->setQuestionId($question->getId());
					if(in_array($vars_voteItem['answer'],Array(0,1,2))){
						$voteItem->setAnswer($vars_voteItem['answer']);
					}
					else{
						return $response->withStatus(400)->write('Question answer required');
					}
					if(!$voteItem->validate()){
						return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
					}
					$voteItems[] = $voteItem;
				}
				elseif($question->getType() == 'office'){
					if(!$vars_voteItem['candidates']){
						return $response->withStatus(400)->write('Question candidates required');
					}
					$ranking = Array();
					$noranking = Array();
					foreach($vars_voteItem['candidates'] as $var_candidate){
						if(!empty($var_candidate['candidateId'])){
							$candidate = \MESBallotBox\Propel\CandidateQuery::create()->filterByQuestionId($question->getId())->filterById($var_candidate['candidateId'])->findOne();
							if(!$candidate) return $response->withStatus(400)->write('Question candidates required');
						}else $var_candidate['candidateId'] = 0;
						
						if(!$var_candidate['answer']){
							$noranking[] = $var_candidate['candidateId'];
						}
						elseif(isset($ranking[$var_candidate['answer']])){
							return $response->withStatus(400)->write('Duplicate candidate ranking');
						}
						else $ranking[$var_candidate['answer']] = $var_candidate['candidateId'];
					}
					ksort($ranking);
					$i = 0;
					foreach($ranking as $rankItem){
						$i++;
						$voteItem = new \MESBallotBox\Propel\VoteItem();
						$voteItem->setQuestionId($question->getId());
						$voteItem->setCandidateId($rankItem);
						$voteItem->setAnswer($i);
						if(!$voteItem->validate()){
							return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
						}
						$voteItems[] = $voteItem;
					}
					if($noranking){
						$none_rank = array_search(0,$noranking);
						if($none_rank!==false){
							unset($noranking[$none_rank]);
							$i++;
							$voteItem = new \MESBallotBox\Propel\VoteItem();
							$voteItem->setQuestionId($question->getId());
							$voteItem->setCandidateId(0);
							$voteItem->setAnswer($i);
							$voteItems[] = $voteItem;
						}
						if($noranking) foreach($noranking as $rankItem){
							$i++;
							$voteItem = new \MESBallotBox\Propel\VoteItem();
							$voteItem->setQuestionId($question->getId());
							$voteItem->setCandidateId($rankItem);
							$voteItem->setAnswer($i);
							if(!$voteItem->validate()){
								return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
							}
							$voteItems[] = $voteItem;
						}
					}
				}
			}
			try{
				$vote->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			foreach($voteItems as $voteItem){
				$voteItem->setVoteId($vote->getId());
				try{
					$voteItem->save();
				}catch(Exception $e){
					return $response->withStatus(500)->write($e->getMessage());
				}
			}
			$result = \MESBallotBox\Controller\Ballot::getVoteResult($ballot,$vote);
			return $response->write(json_encode($result));
		});
		$slim->get('/{ballotId}/vote/{voteId}', function($request, $response,$args){
			$ballot = \MESBallotBox\Controller\Ballot::getVoterBallot($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not available for voting.');
			}
			$vote = \MESBallotBox\Propel\VoteQuery::create()->filterByBallotId($args['ballotId'])->filterById($args['voteId'])->findOne();
			if(!$vote){
				return $response->withStatus(400)->write('Vote not found.');
			}
			if($vote->getUserId() != $_ENV['ballot_user']['id']){
				if($ballot->getUserId() != $_ENV['ballot_user']['id']){
					return $response->withStatus(400)->write('Vote not accessible.');
				}
			}
			$result = \MESBallotBox\Controller\Ballot::getVoteResult($ballot,$vote);
			return $response->write(json_encode($result));
		});
		$slim->post('/{ballotId}/vote/{voteId}', function($request, $response,$args){
			$vars = $request->getParsedBody();
			$ballot = \MESBallotBox\Controller\Ballot::getVoterBallot($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not available for voting.');
			}
			$vote = \MESBallotBox\Propel\VoteQuery::create()->filterByBallotId($args['ballotId'])->filterById($args['voteId'])->findOne();
			if(!$vote){
				return $response->withStatus(400)->write('Vote not found.');
			}
			if($vote->getUserId() != $_ENV['ballot_user']['id']){
				if($ballot->getUserId() != $_ENV['ballot_user']['id']){
					return $response->withStatus(400)->write('Vote not accessible.');
				}
			}
			
			$vote->setVersionCreatedBy($_ENV['ballot_user']['id']);
			$vote->setUpdatedAt(time());
			if(!$vars['voteItem']){
				return $response->withStatus(400)->write('Vote answers required');
			}
			$voteItems = Array();
			foreach($vars['voteItem'] as $vars_voteItem){
				if(!$vars_voteItem['questionId']) return $response->withStatus(400)->write('Vote question required');
				$question = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($ballot->getId())->filterById($vars_voteItem['questionId'])->findOne();
				if(!$question) return $response->withStatus(400)->write('Question invalid');
				
				if($question->getType() == 'proposition'){
					$voteItem = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->findOne(); 
					if(!$voteItem){
						$voteItem = new \MESBallotBox\Propel\VoteItem();
						$voteItem->setQuestionId($question->getId());
					}
					if(in_array($vars_voteItem['answer'],Array(0,1,2))){
						$voteItem->setAnswer($vars_voteItem['answer']);
					}
					else{
						return $response->withStatus(400)->write('Question answer required');
					}
					if(!$voteItem->validate()){
						return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
					}
					$voteItem->setVersionCreatedBy($_ENV['ballot_user']['id']);
					$voteItems[] = $voteItem;
				}
				elseif($question->getType() == 'office'){
					if(!$vars_voteItem['candidates']){
						return $response->withStatus(400)->write('Question candidates required');
					}
					$ranking = Array();
					$noranking = Array();
					foreach($vars_voteItem['candidates'] as $var_candidate){
						if(!empty($var_candidate['candidateId'])){
							$candidate = \MESBallotBox\Propel\CandidateQuery::create()->filterByQuestionId($question->getId())->filterById($var_candidate['candidateId'])->findOne();
							if(!$candidate) return $response->withStatus(400)->write('Question candidates required');
						}else $var_candidate['candidateId'] = 0;
						
						if(!$var_candidate['answer']){
							$noranking[] = $var_candidate['candidateId'];
						}
						elseif(isset($ranking[$var_candidate['answer']])){
							return $response->withStatus(400)->write('Duplicate candidate ranking');
						}
						else $ranking[$var_candidate['answer']] = $var_candidate['candidateId'];
					}
					ksort($ranking);
					$i = 0;
					foreach($ranking as $rankItem){
						$i++;
						$voteItem = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->filterByCandidateId($rankItem)->findOne();
						if(!$voteItem){
							$voteItem = new \MESBallotBox\Propel\VoteItem();
							$voteItem->setQuestionId($question->getId());
							$voteItem->setCandidateId($rankItem);
						}
						$voteItem->setAnswer($i);
						$voteItem->setVersionCreatedBy($_ENV['ballot_user']['id']);
						if(!$voteItem->validate()){
							return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
						}
						$voteItems[] = $voteItem;
					}
					if($noranking){
						$none_rank = array_search(0,$noranking);
						if($none_rank!==false){
							unset($noranking[$none_rank]);
							$i++;
							$voteItem = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->filterByCandidateId(0)->findOne();
							if(!$voteItem){
								$voteItem = new \MESBallotBox\Propel\VoteItem();
								$voteItem->setQuestionId($question->getId());
								$voteItem->setCandidateId(0);
							}
							$voteItem->setAnswer($i);
							$voteItem->setVersionCreatedBy($_ENV['ballot_user']['id']);
							$voteItem = new \MESBallotBox\Propel\VoteItem();
							if(!$voteItem->validate()){
								return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
							}
							$voteItems[] = $voteItem;
						}
						if($noranking) foreach($noranking as $rankItem){
							$i++;
							$voteItem = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->filterByCandidateId($rankItem)->findOne();
							if(!$voteItem){
								$voteItem = new \MESBallotBox\Propel\VoteItem();
								$voteItem->setQuestionId($question->getId());
								$voteItem->setCandidateId($rankItem);
							}
							$voteItem->setAnswer($i);
							$voteItem->setVersionCreatedBy($_ENV['ballot_user']['id']);
							$voteItem = new \MESBallotBox\Propel\VoteItem();
							if(!$voteItem->validate()){
								return $response->withStatus(400)->write($voteItem->getValidationFailures()->__toString());
							}
							$voteItems[] = $voteItem;
						}
					}
				}
			}
			try{
				$vote->save();
			}catch(Exception $e){
				return $response->withStatus(500)->write($e->getMessage());
			}
			foreach($voteItems as $voteItem){
				$voteItem->setVoteId($vote->getId());
				try{
					$voteItem->save();
				}catch(Exception $e){
					return $response->withStatus(500)->write($e->getMessage());
				}
			}
			$result = \MESBallotBox\Controller\Ballot::getVoteResult($ballot,$vote);
			return $response->write(json_encode($result));
		});
		$slim->get('/{ballotId}/results', function($request, $response,$args){
			$ballot = \MESBallotBox\Propel\BallotQuery::create()->findPK($args['ballotId']);
			if(!$ballot){
				return $response->withStatus(400)->write('Ballot not found.');
			}
			if($ballot->getUserId() != $_ENV['ballot_user']['id']){
				return $response->withStatus(400)->write('Ballot results forbidden.');
			}

			$questions = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($ballot->getId())->filterByIsDeleted(0)->find();
			if(!$questions){
				return $response->withStatus(400)->write('No questions for ballot.');
			}
			$results = Array();
			$q = new \MESBallotBox\Propel\VoteQuery();
			$votes = $q->filterByBallotId($args['ballotId'])->find();
			$results = Array();
			foreach($votes as $vote){
				$results['votes'][] = array_merge($vote->getUser()->toArray(), $vote->toArray());
				
			}
			foreach($questions as $question){
				$qid = $question->getId();
				switch($question->getType()){
					case 'proposition':
						$totals = Array(0=>0, 1=>0, 2=>0);
						$voteItems = \MESBallotBox\Propel\VoteItemQuery::create()->filterByQuestionId($qid)->find();
						if($voteItems)foreach($voteItems as $voteItem){
							$totals[$voteItem->getAnswer()]++;
						}
						$result = array_combine(Array('abstain', 'yes', 'no'),$totals);
						$total = $result['yes']+$result['no'];
						if($total == 0){
							$result['result'] = 'no votes';
						}else{
							$result['yes_percent'] = floor($result['yes'] / $total * 100);
							$result['no_percent'] = floor($result['no'] / $total * 100);
							if($result['yes'] > $result['no']) $result['result'] = 'pass';
							else $result['result'] = 'fail';
						}
						$results[$qid] = $result;
						break;
					case 'office':
						$voteItems = \MESBallotBox\Propel\VoteItemQuery::create()->filterByQuestionId($qid)->orderByAnswer()->find();
						$candidates = \MESBallotBox\Propel\CandidateQuery::create()->filterByQuestionId($qid)->find();
						$candidateIds = Array();
						foreach($candidates as $candidate) $candidateIds[] = $candidate->getId();
						$totalCandidates = count($candidateIds);
						$votes = Array();
						if($voteItems)foreach($voteItems as $voteItem){
							$votes[$voteItem->getVoteId()][$voteItem->getAnswer()] = $voteItem->getCandidateId();
						}
						foreach($votes as $voteId => $vote){
							$votes[$voteId] = array_values($vote);
						}
						$rounds = array();
						for($i =0; $i < $totalCandidates; $i++){
							//echo "Round $i\n";
							//print_r($votes);
							$losers = false;
							$totals = \MESBallotBox\Controller\Ballot::calculateTotals($votes,$candidateIds);
							$winner = \MESBallotBox\Controller\Ballot::getWinner($totals,$votes);
							$rounds[$i+1] = $totals;
							//echo "totals\n";
							//print_r($totals);
							//var_dump($winner);
							//exit;
							if($winner || count($totals) == 1){
								//we have a result, or we're down to 1 candidate
								break;
							}
							$losers = \MESBallotBox\Controller\Ballot::getLosers($totals,$candidateIds);
							if(!$losers){
								$winner = 'tie';
								break;
							}
							//echo "losers\n";
							//print_r($losers);
							$votes = \MESBallotBox\Controller\Ballot::recalculateVotes($votes,$losers);
							$candidateIds = array_diff($candidateIds,$losers);
							//echo "new votes\n";
							//print_r($votes);
						}
						//echo "Winner $winner\n";
						//print_r($votes);
						//echo "rounds\n";
						//print_r($rounds);
						$result['result'] = $winner;
						if(is_numeric($winner) && $winner){
								$candidate = \MESBallotBox\Propel\CandidateQuery::create()->findPk($winner);
								$result['winningCandidate'] = array_merge($candidate->getUser()->toArray(), $candidate->toArray());
						}
						$result['rounds'] = $rounds;
						$results[$qid] = $result;
						break;
				}
			}
			
			
			return $response->write(json_encode($results));
		});
	}
	
	function calculateTotals($votes, $candidateIds){
		//echo "votes\n";
		//print_r($votes);
		$totals = Array();
		$winner = false;
		$totalCandidates = count($candidateIds);
		$totalVotes = count($votes);
		foreach($candidateIds as $candidateId){
			for($k = 0; $k < $totalCandidates; $k++){
				$totals[$candidateId][$k] = 0;
			}
		} 
		foreach($votes as $vote){
			foreach($vote as $ranking => $candidateId){
				if($candidateId == 0) break;
				$totals[$candidateId][$ranking]++;
			}
		}
		return $totals;
	}
	function getWinner($totals, $votes){
		$voteCount = count($votes);
		$winner = false;
		foreach($totals as $candidateId => $candidate){
			if($candidate[0] *2 > $voteCount){
				$winner = $candidateId;
				break;
			}
		}
		return $winner;
	}
	function getLosers($totals, $candidateIds, $level = 0){
		//echo "getLosers level $level\n";
		//print_r($totals);
		//print_r($candidateIds);
		$losers = Array();
		$highestVotes = 0;
		$lowestVotes = 9999;
		$totalLevels = count($candidateIds);
		foreach($totals as $candidateId => $ranking){
			if($ranking[$level] > $highestVotes) $highestVotes = $ranking[0];
			if($ranking[$level] < $lowestVotes) $lowestVotes = $ranking[0];
		}
		if($highestVotes == $lowestVotes){
			if($level == 0) return $losers; // we have a tie
			elseif($level == $totalLevels-1){
				foreach($totals as $candidateId => $ranking){
					$losers[] = $candidateId;
				}
				return $losers;
			}else{
				return self::getLosers($totals,$candidateIds,$level+1);
			}
		}
		foreach($totals as $candidateId => $ranking){
			if($ranking[$level] == $lowestVotes){
				$losers[] = $candidateId;
			}
		}
		if(count($losers) > 1){
			foreach($totals as $candidateId => $ranking){
				if(!in_array($candidateId,$losers)) unset($totals[$candidateId]);
			}
			return self::getLosers($totals,$candidateIds,$level+1);
		}
		return $losers;
	}
	function recalculateVotes($votes,$losers){
		foreach($votes as $id => $vote){
			foreach($vote as $ranking => $candidateId){
				if(in_array($candidateId,$losers)){
					unset($vote[$ranking]);
				}
			}
			$votes[$id] = array_values($vote);
		}
		return $votes;
	}
	function getUser($membershipNumber){
		$q = new \MESBallotBox\Propel\UserQuery();
		if(!$membershipNumber) return false;
		$user = $q->filterByMembershipNumber($membershipNumber)->findOne();
		if(!$user){
			$userInfo = \MESBallotBox\Controller\Hub::getUser($membershipNumber);
			if(!$userInfo || ! is_array($userInfo) || !$userInfo['membershipNumber']){
				return false;
			}
			$user = new \MESBallotBox\Propel\User();
			$user->fromArray($userInfo);
			$user->save();
		}
		return $user;
	}
	function getQuestions($ballot){
		if(!$ballot) return false;
		$q = new \MESBallotBox\Propel\QuestionQuery();
		$questions = $q->filterByBallotId($ballot->getId())->orderById()->find();
		if($questions){
			$questionsresult = Array();
			foreach($questions as $question){
				$questionresult = $question->toArray();
				if($questionresult['type'] == 'office'){
					$candidateresults = Array();
					$candidates = \MESBallotBox\Propel\CandidateQuery::create()->filterByQuestionId($question->getId())->orderById()->find();
					foreach($candidates as $candidate){
						$candidateresults[] = array_merge($candidate->getUser()->toArray(), $candidate->toArray());
					}
					$questionresult['candidates'] = $candidateresults;
				}
				$questionsresult[] = $questionresult;
			}
		}
		return $questionsresult;
	}
	function getVoterBallot($ballotId){
		$q = new \MESBallotBox\Propel\BallotQuery();
		$time=time();

		$ballot = $q::create()
			->join('Ballot.Voter')
			->condition('byUser', 'Voter.userId = ?', $_ENV['ballot_user']['id'])
			->condition('byAffiliate', 'Voter.affiliateId = ?', $_ENV['ballot_user']['affiliateId'])
			->where(Array('byUser', 'byAffiliate'), 'or')
			->where('Ballot.startTime < ?', $time)
			->where('Ballot.endTime > ?', $time)
			->where('Ballot.id = ?',$ballotId)
			->findOne();
		return $ballot;
	}
	function getVoteResult($ballot,$vote){
		$result = $vote->toArray();
		$result['voteItem'] = Array();
		$questions = \MESBallotBox\Propel\QuestionQuery::create()->filterByBallotId($ballot->getId())->orderById()->find();
		if(count($questions))foreach($questions as $question){
			
			if($question->getType() == 'proposition'){
				$answer = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->findOne();
				if($answer){
					$voteItemResult = $answer->toArray();
				}
				else{
					$voteItemResult = Array('questionId' => $question->getId(), 'voteId' => $vote->getId(), 'answer' => 0);
				}
			}
			else{
				$voteItemResult = Array('questionId' => $question->getId());
				$voteItemResult['candidates'] = Array();
				$candidates = \MESBallotBox\Propel\CandidateQuery::create()->filterByQuestionId($question->getId())->orderById()->find();
				foreach($candidates as $candidate){
					$candidateResult = Array();
					$answer = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->filterByCandidateId($candidate->getId())->findOne();
					if($answer){
						$candidateResult = $answer->toArray();
					}
					else{
						$candidateResult = Array('questionId' => $question->getId(), 'voteId' => $vote->getId(), 'candidateId' => $candidate->getId(), 'answer' => 0);
					}
					$voteItemResult['candidates'][] = $candidateResult;
				}
				$candidateResult = Array();
				$answer = \MESBallotBox\Propel\VoteItemQuery::create()->filterByVoteId($vote->getId())->filterByQuestionId($question->getId())->filterByCandidateId(0)->findOne();
				if($answer){
					$candidateResult = $answer->toArray();
				}
				else{
					$candidateResult = Array('questionId' => $question->getId(), 'voteId' => $vote->getId(), 'candidateId' => 0, 'answer' => 0);
				}
				$voteItemResult['candidates'][] = $candidateResult;
			}
			$result['voteItem'][] = $voteItemResult;
		}
		return $result;
	}
	
	function checkInvalidVoter($ballot){
		$expire_time = strtotime($_ENV['ballot_user']['membershipExpiration']);
		if($expire_time < time()){
			return "Cannot vote while membership is expired.";
		}
		if($expire_time < $ballot->getEndTime()){
			return "You will be expired before the vote has closed, and therefore cannot vote.";
		}
		if($_ENV['ballot_user']['membershipType'] != 'Full'){
			return "You are a ".$_ENV['ballot_user']['membershipType']." member. Only full members can vote";
		}
		return false;
	}
}