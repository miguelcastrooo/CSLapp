select * from alumnos;
select * from users;
select nivel_educativo from alumnos;
select grado from alumnos;

SELECT grado, COUNT(*) 
FROM alumnos 
GROUP BY nivel_educativo;

DELETE FROM alumnos 
WHERE id NOT IN (
    SELECT MIN(id) 
    FROM alumnos 
    GROUP BY nivel_educativo
);




describe alumnos;

UPDATE users SET role = 'admin' WHERE id = 1;  -- Asigna el rol admin al usuario con ID 1


SELECT DISTINCT nivel_educativo FROM alumnos;
DELETE FROM alumnos WHERE nivel_educativo = 'Preescolar' AND id NOT IN (
    SELECT MIN(id) FROM alumnos GROUP BY nivel_educativo
);

ALTER TABLE alumnos MODIFY grado ENUM('Babies Room', '1° Kínder', '2° Kínder', '3° Kínder', '1°', '2°', '3°', '4°', '5°', '6°', '1° Secundaria', '2° Secundaria', '3° Secundaria');


ALTER TABLE alumnos 
MODIFY nivel_educativo ENUM('Preescolar', 'Primaria Baja', 'Primaria Alta', 'Secundaria') 
NOT NULL;


ALTER TABLE users DROP COLUMN role;
