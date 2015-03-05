<?php
class Mongo {
    private static $connection = null;
    private static $db_name = 'test';

    private static function _getConnection() {
        if(!self::$connection) {
            self::$connection = new \Mongo();
        }

        return self::$connection;
    }

    private static function _getDB() {
        return self::_getConnection()->selectDb(self::$db_name);
    }

    private static function _getCollection($collection) {
        return new \MongoCollection(self::_getDB(), $collection);
    }

    public static function setDBName($name) {
        self::$db_name = $name;
    }

    public static function fetch($collection, $criteria = array(), $fields = array(), $limit = 0, $skip = 0, $sort = null) {
        $collection = self::_getCollection($collection);
        $cursor = $collection->find($criteria, $fields);
        if($limit && $limit > 0) {
            $cursor->limit($limit);
        }

        if($skip && $skip > 0) {
            $cursor->skip($skip);
        }

        if($sort) {
            $cursor->sort($sort);
        }

        $result = array();
        while($cursor->hasNext()) {
            $result[] = $cursor->getNext();
        }

        return $result;
    }

    public static function fetchOne($collection, $criteria = array(), $fields = array()) {
        $collection = self::_getCollection($collection);
        return $collection->findOne($criteria, $fields);
    }

    public static function insert($collection, $data) {
        $collection = self::_getCollection($collection);
        return $collection->insert($data);
    }

    public static function update($collection, $criteria, $data) {
        return self::_update($collection, $criteria, $data, array(
            'multiple' => true
        ));
    }

    public static function upsert($collection, $criteria, $data) {
        return self::_update($collection, $criteria, $data, array(
            'upsert' => true
        ));
    }

    public static function remove($collection, $criteria = array()) {
        return self::_remove($collection, $criteria);
    }

    public static function removeOne($collection, $criteria = array()) {
        return self::_remove($collection, $criteria, array(
            'justOne' => true
        ));
    }

    public static function count($collection, $criteria = array()) {
        $collection = self::_getCollection($collection);
        return $collection->count($criteria);
    }

	public static function getLastError() {
		return self::_getDB()->lastError();
	}

	public static function id($id) {
		if($id instanceof \MongoId) {
			return $id;
		} else {
			try {
				$id = new \MongoId($id);
			} catch(\Exception $e) {
				$id = new \MongoId();
			}
			return $id;
		}
	}

	public static function newId() {
		return new \MongoId();
	}

    private static function _remove($collection, $criteria = array(), $options = array()) {
        $collection = self::_getCollection($collection);
        return $collection->remove($criteria, $options);
    }

    private  static function _update($collection, $criteria, $data, $options = array()) {
        $collection = self::_getCollection($collection);
        return $collection->update($criteria, $data, $options);
    }
}