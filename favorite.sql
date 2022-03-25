CREATE TABLE favorites (
id int NOT NULL AUTO_INCREMENT, 
member_id int NOT NULL, 
post_id int NOT NULL, 
created_at timestamp NOT NULL, 
updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
PRIMARY KEY (id)
);