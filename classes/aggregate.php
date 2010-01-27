<?php

class Aggregate {
	protected $db;
	protected $insert;

	public function __construct() {
		$this->db = new SQLite3(':memory:');
		$this->db->exec(
			'CREATE TABLE "entries" ('
			.'"id" INTEGER PRIMARY KEY, '
			.'"parent_id" INTEGER, '
			.'"time" INTEGER, '
			.'"name" STRING, '
			.'"value" STRING'
			.')'
		);

		$this->db->exec(
			'CREATE INDEX "entries_parent_id" ON "entries" ("parent_id")'
		);
	}

	public function __get($key) {
		if ($key === 'items') {
			$items = array();
			$query = $this->db->query(
				'SELECT * FROM "entries" WHERE "parent_id" IS NULL ORDER BY "time" DESC'
			);

			$result = array();
			while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
				$result[] = $row;
			}

			return $result;
		}
	}

	protected function prepare() {
		$this->insert = $this->db->prepare(
			'INSERT INTO "entries" '
			.'("parent_id", "time", "name", "value") '
			.'VALUES (:parent_id, :time, :name, :value)'
		);
	}

	protected function bind($name, $value, $type) {
		if (is_null($type))
			$type = SQLITE3_NULL;
		$this->insert->bindValue(':'.$name, $value, $type);
	}

	protected function insert($parent_id, $time, $name, $value = NULL) {
		$this->bind('parent_id', $parent_id, SQLITE3_INTEGER);
		$this->bind('time', $time, SQLITE3_INTEGER);
		$this->bind('name', $name, SQLITE3_TEXT);
		$this->bind('value', $value, SQLITE3_TEXT);
		$this->insert->execute();
		return $this->db->lastInsertRowID();
	}

	public function add_entry($entry) {
		if (! (bool) $this->insert) {
			$this->prepare();
		}

		Core::log('debug', 'Adding root entry \'%s\'', $entry->title);
		$entry_id = $this->insert(NULL, strtotime($entry->published), $entry->title, NULL);

		foreach ($entry->link as $key => $link) {
			Core::log('debug', 'Link %s: %s', $key, $link);
			foreach ($link->attributes() as $attr => $attrval) {
				Core::log('debug', "\t".'%s: %s', $attr, $attrval);
			}
		}

		$link_num = 0;
		foreach ($entry->link as $link) {
			Core::log('debug', 'Appending link %d', $link_num++);
			$link_id = $this->insert($entry_id, NULL, 'link', NULL);
			foreach ($link->attributes() as $attrname => $attrval) {
				$this->insert($link_id, NULL, $attrname, $attrval);
			}
		}

		/**
		foreach ($entry as $key => $value) {
			$subnode = $node->addChild((string) $key, str_replace('&', '&#38;', (string) $value));
			foreach ($value->attributes() as $attrkey => $attrval) {
				$subnode->addAttribute($attrkey, (string) $attrval);
			}
		}
		 */
	}
}
