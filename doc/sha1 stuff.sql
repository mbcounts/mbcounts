-- select * from counselors;

select c.FirstName, c.LastName, sha1(address1 + ' ' + address2 + ' ' + city + ' ' + state) from counselors c 
left join geodatas g on sha1(address1 + ' ' + address2 + ' ' + city + ' ' + state) = g.sha1 
where g.sha1 is not null
order by c.LastName, c.FirstName


            select
                Counselor.ID,
                Counselor.Address1 as Address1,
                Counselor.Address2 as Address2,
                Counselor.City as City,
                Counselor.State as State,
                Counselor.ZIPCode as ZIPCode  ,
                Counselor.FirstName,
                Counselor.LastName,
                sha1( upper(trim(address1)) + ' ' + upper(trim(address2)) + ' ' + upper(trim(city)) + ' ' + upper(trim(state)) ) as sha1
            from counselors Counselor
            left join geodatas g
                on sha1( upper(trim(address1)) + ' ' + upper(trim(address2)) + ' ' + upper(trim(city)) + ' ' + upper(trim(state)) ) = g.sha1
            where g.sha1 is null
            order by Counselor.LastName, Counselor.FirstName
            LIMIT 30



select * from geodatas;

select sha1('a'), sha1('A');

insert into geodatas (sha1, lat, lon) values ('881cd002ab0b4dc9dab6a131ca7b162f75e77ec9', 33.333, 111.444);

show create table geodatas;

select * from ;