ALTER TABLE `project_versions` AUTO_INCREMENT=1

CREATE TABLE users (
	username VARCHAR(10) NOT NULL,	
	password VARCHAR(30),
	fb_id VARCHAR(255),
	verified TINYINT(1) DEFAULT "0",
	email VARCHAR(30),
PRIMARY KEY (username));

INSERT INTO users (username, password) VALUES ('Mayron', 'letmein');

CREATE TABLE admins (
	username VARCHAR(10) NOT NULL,
	admin_password VARCHAR(30),
	change_log TEXT,
PRIMARY KEY (username));

CREATE TABLE news (
	id INT(11) NOT NULL AUTO_INCREMENT,
	author VARCHAR(10) NOT NULL,
	post_data DATETIME DEFAULT CURRENT_TIMESTAMP,
	content TEXT,
PRIMARY KEY (id));

CREATE TABLE files (
	file_path VARCHAR(250) NOT NULL DEFAULT '',
	author VARCHAR(10) NOT NULL,
	upload_data DATETIME DEFAULT CURRENT_TIMESTAMP,
	verified TINYINT(1),
PRIMARY KEY (file_path));


CREATE TABLE private_chat_logs (
	id INT(11) NOT NULL AUTO_INCREMENT,
	recipient VARCHAR(10) NOT NULL,
	username VARCHAR(10) NOT NULL,
PRIMARY KEY (id));

CREATE TABLE messages (
	id INT(11) NOT NULL AUTO_INCREMENT,
	chat_log_id INT(11) NOT NULL,
	receiver VARCHAR(10) NOT NULL,
	sender VARCHAR(10) NOT NULL,
	contents TEXT,
INDEX (chat_log_id),
PRIMARY KEY (id));

CREATE TABLE contact_messages (
	id INT(11) NOT NULL AUTO_INCREMENT,
	sender VARCHAR(20) NOT NULL,
	email VARCHAR(255) NOT NULL,
	title VARCHAR(100) NOT NULL,
	message TEXT NOT NULL,
	date_sent DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	PRIMARY KEY (id));

CREATE TABLE profiles (
	username VARCHAR(10) NOT NULL,
	about_me TEXT,
	location VARCHAR(10),
	realm VARCHAR(10),
	characters TEXT,
	website_urls TEXT,
	profile_picture VARCHAR(250),
PRIMARY KEY (username));

INSERT INTO profiles (username, profile_picture) VALUES ("Mayron", "images/paris.jpg");

/* may have to delete this? or rename it to blog entry */
CREATE TABLE pages (
	id INT(11) NOT NULL AUTO_INCREMENT,
	content TEXT,
	menu_name VARCHAR (10) NOT NULL,
	menu_position UNSIGNED SMALLINT NOT NULL,
PRIMARY KEY (id));

CREATE TABLE nav_items (
	id TINYINT NOT NULL AUTO_INCREMENT,
	sublist TINYINT(1),
	link TEXT,
	name VARCHAR(20) NOT NULL,
PRIMARY KEY (id));

INSERT INTO nav_items (name) VALUES ('Home'), ('UI Showcase'), ('Community'), ('Downloads'), ('Contact Us');

CREATE TABLE sub_nav_items (
	id TINYINT NOT NULL AUTO_INCREMENT,
	parent_id INT(11) NOT NULL,
	link TEXT,
	name VARCHAR(20) NOT NULL,
	INDEX (parent_id),
PRIMARY KEY (id));

CREATE TABLE projects_viewed (
	project_id INT(11) NOT NULL,
	username VARCHAR(10) NOT NULL,
	liked TINYINT(1) NOT NULL,
	INDEX (project_id),
	INDEX (username),
	CONSTRAINT groupID PRIMARY KEY (project_id, username)
);

INSERT INTO sub_nav_items (parent_id, name) VALUES (3, 'News'), (3, 'Blogs'), (3, 'Forum'), (4, 'Featured Projects'), (4, 'All Projects');
INSERT INTO sub_nav_items (parent_id, name) VALUES (3, "Videos");


CREATE TABLE showcase_comments (
	id INT(11) NOT NULL AUTO_INCREMENT,
	project_id INT(11) NOT NULL,
	commenter VARCHAR(10) NOT NULL,
	comment_text TEXT NOT NULL,
	comment_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,	
	INDEX (project_id),
	INDEX (commenter),
PRIMARY KEY (id));

INSERT INTO showcase_comments (project_id, commenter, comment_text) VALUES (1, "Mayron", "I love that I can now add any comment I want muhaha");

CREATE TABLE project_images (
	image_id TINYINT(1) NOT NULL, 
	project_id INT(11) NOT NULL,
	file_path VARCHAR(250) NOT NULL DEFAULT "images/unknown.jpg",
	INDEX (project_id),
PRIMARY KEY (image_id, project_id));

INSERT INTO project_images (image_id, project_id, file_path) VALUES (1, 1, "images/showcase/screenshot.jpg");
INSERT INTO project_images (image_id, project_id, file_path) VALUES (1, 2, "images/showcase/another.jpg");

CREATE TABLE projects (
	id INT(11) NOT NULL AUTO_INCREMENT, 
	title VARCHAR(50) NOT NULL,
	author VARCHAR(10) NOT NULL,
	downloads INT(11) NOT NULL DEFAULT 0,
	content TEXT,
	credits TEXT,
	update_date DATETIME DEFAULT CURRENT_TIMESTAMP,
	created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
	description VARCHAR(300),
	banner_path VARCHAR(250) NOT NULL DEFAULT "images/default_banner.jpg",
	availability TINYINT(1) NOT NULL DEFAULT 1,
	verified TINYINT(1) NOT NULL DEFAULT 0,
	request_removal TINYINT (1) NOT NULL DEFAULT 0,
	featured TINYINT (1) NOT NULL DEFAULT 0,
PRIMARY KEY (id));


CREATE TABLE project_changes (
	id INT(11) NOT NULL AUTO_INCREMENT,
	project_id INT(11) NOT NULL,
	content TEXT NOT NULL,
	INDEX (project_id),
PRIMARY KEY (id));

CREATE TABLE project_versions (	
	version_id INT(11) NOT NULL AUTO_INCREMENT,	
	project_id INT(11) NOT NULL,
	created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
	version_name VARCHAR(8) NOT NULL DEFAULT "1",	
	file_path VARCHAR(250),	
	current_version TINYINT(1) NOT NULL DEFAULT 1,
	verified TINYINT(1) NOT NULL DEFAULT 0,
	INDEX (project_id),
PRIMARY KEY (version_id));


CREATE TABLE project_install_steps (
	step INT(11) NOT NULL,
	project_id INT(11) NOT NULL,
	content TEXT NOT NULL,
	INDEX (project_id),
PRIMARY KEY (step));

CREATE TABLE project_videos (
	id INT(11) NOT NULL,
	project_id INT(11) NOT NULL,
	url TEXT NOT NULL,
	INDEX (project_id),
PRIMARY KEY (id));











