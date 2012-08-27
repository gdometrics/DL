<?php
//
// There are 3 versions of the DL app corresponding to the 3 branches of the app in Git
// Version 1(master branch): Allows a book to have only one author and category each. No caching is supported 
// Version 2('Caching-and-Many-Many-relations' branch):
//	- Allows a book to have a maximum of 3 authors and categories each
//	- Caching is enabled
// Version 3
//	- Uses Yii's RBAC for access control
//  - The app's Role component is not used to manage roles
//
// Each version has a different database schema
//	- This one needs to be used when you are coming up from Version 2 (i.e., 'Caching-and-Many-Many-relations' branch)
// 		- Type yiic migrate up  at the command prompt to make the DB schema to be in sync with this version, provided:
//			1. You have checked out Version 3 ('Use-Yii-RBAC') version of the app
//			2. Your DB schema belongs to Vesion 2 
//
//	The function does the following:
//	1. Inserts an 'AdminDefault' role into the Role table
//		By default, any admin user added to the system using this version of the app is assigned to the AdminDefault role - the least privileged role in the system
//		Even though this version of the app doesn't use the role table, the admin_user table's roleId still refers to it. In order for an admin user to be 
//		successfully added to the system, AdminDefault role has to be added to the role table
//	2. Creates the 3 tables required for Yii RBAC
//

class m120826_092147_Migrate_Database extends CDbMigration
{
	public function safeUp()
	{

		//Though the role table is not used in Version 3 of the app, it's required for the earlier versions
		//Leave it alone
		$this->insert('role', array('rolename' => 'AdminDefault', 'description' => 'The least privileged role in this version of the app. They have access only to the landing page of the admin module!'));
		$this->createTable('authitem',
								array
								(
									'name' => 'varchar(64) NOT NULL',
									'type' => 'INT(11) NOT NULL',
									'description' => 'text',						
									'bizrule' => 'text',
									'data' => 'text',
									'PRIMARY KEY (name)',
								)
							);
		$this->createTable('authassignment',
							array
							(
							  'itemname' => 'varchar(64) NOT NULL',
							  'userid' => 'mediumint(8) unsigned NOT NULL',
							  'bizrule' => 'text',
							  'data' => 'text',
							  'PRIMARY KEY (itemname,userid)',
							  'KEY FK_authassignment_admin_user (userid)',
							  'CONSTRAINT authassignment_ibfk_1 FOREIGN KEY (itemname) REFERENCES authitem (name) ON DELETE CASCADE ON UPDATE CASCADE',
							  'CONSTRAINT FK_authassignment_admin_user FOREIGN KEY (userid) REFERENCES admin_user (id) ON DELETE CASCADE ON UPDATE CASCADE'
							
							)
						  	
						  );
		$this->createTable('authitemchild',
							array
							(
							  'parent' => 'varchar(64) NOT NULL',
							  'child' => 'varchar(64) NOT NULL',
							  'PRIMARY KEY (parent, child)',
							  'KEY child (child)',
							  'CONSTRAINT authitemchild_ibfk_1 FOREIGN KEY (parent) REFERENCES authitem (name) ON DELETE CASCADE ON UPDATE CASCADE',
							  'CONSTRAINT authitemchild_ibfk_2 FOREIGN KEY (child) REFERENCES authitem (name) ON DELETE CASCADE ON UPDATE CASCADE'
							
							)
						  	
						  );
						  

	}
	public function safeDown()	
	{
		
	}
}