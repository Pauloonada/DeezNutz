create database db_deezNutz;
use db_deezNutz;

create table users(
	id int primary key auto_increment,
    username varchar(30) not null unique,
    email varchar(100) not null unique,
    password varchar(30) not null,
    login_count int default 0,
    login_attempts int default 0,
    last_attempt datetime default null,
    is_locked boolean default false
);