<?php
namespace glasteel;

trait RollsCapsTrait
{
	protected $all_caps = null;
	protected $all_roles = null;

	public function can($cap){
		if ( $this->all_caps === null ){
			$this->all_caps = [];
			$caps = $this->getAllCapabilities();
			if ( $caps ){
				foreach ($caps as $idx => $row) {
					$this->all_caps[$row['slug']] = true;
				}
			}
		}
		if ( array_key_exists($cap, $this->all_caps) ){
			return true;
		}
		return false;
	}//can()

	public function is($role){
		if ( $this->all_roles === null ){
			$this->all_roles = [];
			$roles = $this->getAllRoles();
			if ( $roles ){
				foreach ($roles as $idx => $row) {
					$this->all_roles[$row['slug']] = true;
				}
			}
		}
		if ( array_key_exists($role, $this->all_roles) ){
			return true;
		}
		return false;
	}//is()

	private function getAllCapabilities(){
		$sql =
			"SELECT DISTINCT c.slug

			FROM capability AS c

				JOIN role_capability AS rc ON c.slug = rc.capability_slug

				JOIN role AS r ON r.slug = rc.role_slug

				JOIN {$this->primary_bean_table}_role AS jr ON jr.role_slug = r.slug

				JOIN $this->primary_bean_table AS p ON p.id = jr.user_id

			WHERE p.id = :this_id
				AND c.active = 1
				AND rc.active = 1
				AND r.active = 1
				AND jr.active = 1;
		";//$sql

		return $this->db->getAll($sql,[
			':this_id' => $this->id,
		]);
	}//getAllCapabilities()

	private function getAllRoles(){
		$sql = 
			"SELECT DISTINCT r.slug

			FROM role AS r

				JOIN {$this->primary_bean_table}_role AS jr ON jr.role_slug = r.slug
			
			WHERE jr.user_id = :this_id
				AND r.active = 1
				AND jr.active = 1;
		";//sql

		return $this->db->getAll($sql,[
			':this_id' => $this->id,
		]);
	}//getAllRoles()

}//trait RollsCapsTrait