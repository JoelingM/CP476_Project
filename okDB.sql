# Table picture
CREATE TABLE `picture` (
    `picture_id` BIGINT NOT NULL AUTO_INCREMENT,
    `path` TEXT,
    PRIMARY KEY(`picture_id`)
);

INSERT INTO picture (`path`) VALUES ("pictures/1.png");

# Table user 
CREATE TABLE `user` (
    `user_id` BIGINT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(256) NOT NULL UNIQUE,
    `password` VARCHAR(256) NOT NULL,
    `fname` VARCHAR(256) NOT NULL,
    `lname` VARCHAR(256) NOT NULL,
    `picture_id` BIGINT NOT NULL,
    `birthday` DATE,
    `city` VARCHAR(256),
    `education` TEXT,
    `hobbies` TEXT,
    PRIMARY KEY(`user_id`),
    FOREIGN KEY(`picture_id`) REFERENCES picture(`picture_id`)
);

# Table pal
CREATE TABLE `pal`(
    `user_id` BIGINT NOT NULL,
    `pal_id` BIGINT,
    FOREIGN KEY(`user_id`) REFERENCES user(`user_id`),
    FOREIGN KEY(`pal_id`) REFERENCES user(`user_id`),
    PRIMARY KEY(`user_id`, `pal_id`),
    CONSTRAINT pal_NOT_EQUAL CHECK (`user_id` <> `pal_id`)
);

# Table post 
CREATE TABLE `post`(
  `post_id` BIGINT NOT NULL AUTO_INCREMENT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` BIGINT,
  `content` TEXT,
  `picture_id` BIGINT,
  PRIMARY KEY(`post_id`),
  FOREIGN KEY(`user_id`) REFERENCES user(`user_id`),
  CONSTRAINT post_NOT_EMPTY CHECK (`content` IS NOT NULL OR `picture_id` IS NOT NULL) 
);

# Table reply
CREATE TABLE `reply`(
    `reply_id` BIGINT NOT NULL AUTO_INCREMENT,
    `post_id` BIGINT NOT NULL,
    `user_id` BIGINT NOT NULL,
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `content` TEXT NOT NULL,
    PRIMARY KEY(`reply_id`),
    FOREIGN KEY(`user_id`) REFERENCES user(`user_id`),
    FOREIGN KEY(`post_id`) REFERENCES post(`post_id`)
);

# Table postlike
CREATE TABLE `postlike` (
    `post_id` BIGINT NOT NULL,
    `user_id` BIGINT NOT NULL,
    PRIMARY KEY(`post_id`, `user_id`),    
    FOREIGN KEY(`user_id`) REFERENCES user(`user_id`),
    FOREIGN KEY(`post_id`) REFERENCES post(`post_id`)
)