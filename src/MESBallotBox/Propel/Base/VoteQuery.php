<?php

namespace MESBallotBox\Propel\Base;

use \Exception;
use \PDO;
use MESBallotBox\Propel\Vote as ChildVote;
use MESBallotBox\Propel\VoteQuery as ChildVoteQuery;
use MESBallotBox\Propel\Map\VoteTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'Vote' table.
 *
 *
 *
 * @method     ChildVoteQuery orderByid($order = Criteria::ASC) Order by the id column
 * @method     ChildVoteQuery orderByballotId($order = Criteria::ASC) Order by the ballot_id column
 * @method     ChildVoteQuery orderByuserId($order = Criteria::ASC) Order by the user_id column
 * @method     ChildVoteQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildVoteQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildVoteQuery groupByid() Group by the id column
 * @method     ChildVoteQuery groupByballotId() Group by the ballot_id column
 * @method     ChildVoteQuery groupByuserId() Group by the user_id column
 * @method     ChildVoteQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildVoteQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildVoteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildVoteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildVoteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildVoteQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildVoteQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildVoteQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildVoteQuery leftJoinBallot($relationAlias = null) Adds a LEFT JOIN clause to the query using the Ballot relation
 * @method     ChildVoteQuery rightJoinBallot($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Ballot relation
 * @method     ChildVoteQuery innerJoinBallot($relationAlias = null) Adds a INNER JOIN clause to the query using the Ballot relation
 *
 * @method     ChildVoteQuery joinWithBallot($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Ballot relation
 *
 * @method     ChildVoteQuery leftJoinWithBallot() Adds a LEFT JOIN clause and with to the query using the Ballot relation
 * @method     ChildVoteQuery rightJoinWithBallot() Adds a RIGHT JOIN clause and with to the query using the Ballot relation
 * @method     ChildVoteQuery innerJoinWithBallot() Adds a INNER JOIN clause and with to the query using the Ballot relation
 *
 * @method     ChildVoteQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method     ChildVoteQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method     ChildVoteQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method     ChildVoteQuery joinWithUser($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the User relation
 *
 * @method     ChildVoteQuery leftJoinWithUser() Adds a LEFT JOIN clause and with to the query using the User relation
 * @method     ChildVoteQuery rightJoinWithUser() Adds a RIGHT JOIN clause and with to the query using the User relation
 * @method     ChildVoteQuery innerJoinWithUser() Adds a INNER JOIN clause and with to the query using the User relation
 *
 * @method     ChildVoteQuery leftJoinVoteItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the VoteItem relation
 * @method     ChildVoteQuery rightJoinVoteItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the VoteItem relation
 * @method     ChildVoteQuery innerJoinVoteItem($relationAlias = null) Adds a INNER JOIN clause to the query using the VoteItem relation
 *
 * @method     ChildVoteQuery joinWithVoteItem($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the VoteItem relation
 *
 * @method     ChildVoteQuery leftJoinWithVoteItem() Adds a LEFT JOIN clause and with to the query using the VoteItem relation
 * @method     ChildVoteQuery rightJoinWithVoteItem() Adds a RIGHT JOIN clause and with to the query using the VoteItem relation
 * @method     ChildVoteQuery innerJoinWithVoteItem() Adds a INNER JOIN clause and with to the query using the VoteItem relation
 *
 * @method     \MESBallotBox\Propel\BallotQuery|\MESBallotBox\Propel\UserQuery|\MESBallotBox\Propel\VoteItemQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildVote findOne(ConnectionInterface $con = null) Return the first ChildVote matching the query
 * @method     ChildVote findOneOrCreate(ConnectionInterface $con = null) Return the first ChildVote matching the query, or a new ChildVote object populated from the query conditions when no match is found
 *
 * @method     ChildVote findOneByid(int $id) Return the first ChildVote filtered by the id column
 * @method     ChildVote findOneByballotId(int $ballot_id) Return the first ChildVote filtered by the ballot_id column
 * @method     ChildVote findOneByuserId(int $user_id) Return the first ChildVote filtered by the user_id column
 * @method     ChildVote findOneByCreatedAt(string $created_at) Return the first ChildVote filtered by the created_at column
 * @method     ChildVote findOneByUpdatedAt(string $updated_at) Return the first ChildVote filtered by the updated_at column *

 * @method     ChildVote requirePk($key, ConnectionInterface $con = null) Return the ChildVote by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOne(ConnectionInterface $con = null) Return the first ChildVote matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVote requireOneByid(int $id) Return the first ChildVote filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByballotId(int $ballot_id) Return the first ChildVote filtered by the ballot_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByuserId(int $user_id) Return the first ChildVote filtered by the user_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByCreatedAt(string $created_at) Return the first ChildVote filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByUpdatedAt(string $updated_at) Return the first ChildVote filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVote[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildVote objects based on current ModelCriteria
 * @method     ChildVote[]|ObjectCollection findByid(int $id) Return ChildVote objects filtered by the id column
 * @method     ChildVote[]|ObjectCollection findByballotId(int $ballot_id) Return ChildVote objects filtered by the ballot_id column
 * @method     ChildVote[]|ObjectCollection findByuserId(int $user_id) Return ChildVote objects filtered by the user_id column
 * @method     ChildVote[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildVote objects filtered by the created_at column
 * @method     ChildVote[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildVote objects filtered by the updated_at column
 * @method     ChildVote[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class VoteQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \MESBallotBox\Propel\Base\VoteQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\MESBallotBox\\Propel\\Vote', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildVoteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildVoteQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildVoteQuery) {
            return $criteria;
        }
        $query = new ChildVoteQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildVote|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = VoteTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(VoteTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVote A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, ballot_id, user_id, created_at, updated_at FROM Vote WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildVote $obj */
            $obj = new ChildVote();
            $obj->hydrate($row);
            VoteTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildVote|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(VoteTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(VoteTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByid(1234); // WHERE id = 1234
     * $query->filterByid(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterByid(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByid($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the ballot_id column
     *
     * Example usage:
     * <code>
     * $query->filterByballotId(1234); // WHERE ballot_id = 1234
     * $query->filterByballotId(array(12, 34)); // WHERE ballot_id IN (12, 34)
     * $query->filterByballotId(array('min' => 12)); // WHERE ballot_id > 12
     * </code>
     *
     * @see       filterByBallot()
     *
     * @param     mixed $ballotId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByballotId($ballotId = null, $comparison = null)
    {
        if (is_array($ballotId)) {
            $useMinMax = false;
            if (isset($ballotId['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_BALLOT_ID, $ballotId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ballotId['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_BALLOT_ID, $ballotId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_BALLOT_ID, $ballotId, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByuserId(1234); // WHERE user_id = 1234
     * $query->filterByuserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByuserId(array('min' => 12)); // WHERE user_id > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByuserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \MESBallotBox\Propel\Ballot object
     *
     * @param \MESBallotBox\Propel\Ballot|ObjectCollection $ballot The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVoteQuery The current query, for fluid interface
     */
    public function filterByBallot($ballot, $comparison = null)
    {
        if ($ballot instanceof \MESBallotBox\Propel\Ballot) {
            return $this
                ->addUsingAlias(VoteTableMap::COL_BALLOT_ID, $ballot->getid(), $comparison);
        } elseif ($ballot instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VoteTableMap::COL_BALLOT_ID, $ballot->toKeyValue('PrimaryKey', 'id'), $comparison);
        } else {
            throw new PropelException('filterByBallot() only accepts arguments of type \MESBallotBox\Propel\Ballot or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Ballot relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function joinBallot($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Ballot');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Ballot');
        }

        return $this;
    }

    /**
     * Use the Ballot relation Ballot object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \MESBallotBox\Propel\BallotQuery A secondary query class using the current class as primary query
     */
    public function useBallotQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBallot($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Ballot', '\MESBallotBox\Propel\BallotQuery');
    }

    /**
     * Filter the query by a related \MESBallotBox\Propel\User object
     *
     * @param \MESBallotBox\Propel\User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVoteQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof \MESBallotBox\Propel\User) {
            return $this
                ->addUsingAlias(VoteTableMap::COL_USER_ID, $user->getid(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VoteTableMap::COL_USER_ID, $user->toKeyValue('PrimaryKey', 'id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type \MESBallotBox\Propel\User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \MESBallotBox\Propel\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\MESBallotBox\Propel\UserQuery');
    }

    /**
     * Filter the query by a related \MESBallotBox\Propel\VoteItem object
     *
     * @param \MESBallotBox\Propel\VoteItem|ObjectCollection $voteItem the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVoteQuery The current query, for fluid interface
     */
    public function filterByVoteItem($voteItem, $comparison = null)
    {
        if ($voteItem instanceof \MESBallotBox\Propel\VoteItem) {
            return $this
                ->addUsingAlias(VoteTableMap::COL_ID, $voteItem->getvoteId(), $comparison);
        } elseif ($voteItem instanceof ObjectCollection) {
            return $this
                ->useVoteItemQuery()
                ->filterByPrimaryKeys($voteItem->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByVoteItem() only accepts arguments of type \MESBallotBox\Propel\VoteItem or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the VoteItem relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function joinVoteItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('VoteItem');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'VoteItem');
        }

        return $this;
    }

    /**
     * Use the VoteItem relation VoteItem object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \MESBallotBox\Propel\VoteItemQuery A secondary query class using the current class as primary query
     */
    public function useVoteItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinVoteItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'VoteItem', '\MESBallotBox\Propel\VoteItemQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildVote $vote Object to remove from the list of results
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function prune($vote = null)
    {
        if ($vote) {
            $this->addUsingAlias(VoteTableMap::COL_ID, $vote->getid(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the Vote table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VoteTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            VoteTableMap::clearInstancePool();
            VoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VoteTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(VoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            VoteTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            VoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(VoteTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(VoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(VoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(VoteTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(VoteTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildVoteQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(VoteTableMap::COL_CREATED_AT);
    }

} // VoteQuery
