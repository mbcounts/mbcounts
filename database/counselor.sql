
create table counselors(
  id INT primary key not null,
  first_name varchar(20) null,
  last_name varchar(20) null,
  city varchar(20) null,
  zip varchar(10) null,
  phone varchar(20) null,
  address varchar(40) null,
  state varchar(2) null
);

create table raw_counselors(
  `Badge` varchar(40) null,
  `Troop Only` varchar(1) null,
  `First Name` varchar(25) null,
  `Last Name` varchar(25) null,
  `City` varchar(25) null,
  `ZIP Code` varchar(25) null,
  `Phone No` varchar(25) null,
  `Person ID` int null
);

create table meritbadges(
  id INT primary key not null auto_increment,
  name varchar(40)
);

create table meritbadge_counselors(
  id INT primary key not null auto_increment,
  counselor_id int not null,
  meritbadge_id int not null,
  FOREIGN KEY (counselor_id)
  REFERENCES counselors(id),
  FOREIGN KEY (meritbadge_id)
  REFERENCES meritbadges(id) ,
  UNIQUE (counselor_id, meritbadge_id)
);

