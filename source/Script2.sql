create database bancoapi;
use bancoapi;
create table users(
    id int primary key auto increment,
    first_name varchar(50) not null,
    last_name varchar(50) not null,
    email varchar(50) null
);