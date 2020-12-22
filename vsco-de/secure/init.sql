BEGIN TRANSACTION;
CREATE TABLE images (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    file_name TEXT NOT NULL,
    file_ext TEXT NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL
);

CREATE TABLE tags(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    keyword TEXT NOT NULL
);

CREATE TABLE image_tags(
    id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
    image_id INTEGER REFERENCES images(id),
    tag_id INTEGER REFERENCES tags(id)
);
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (1, 'amsterdambike.jpeg', 'jpeg', 'Biking in Amsterdam', '2018-06-15');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (2, 'amserdamflowers.jpeg', 'jpeg', 'Flowers of Amsterdam', '2018-06-16');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (3, 'ithacagreen.jpeg', 'jpeg', 'Greenery in Ithaca, NY', '2018-09-18');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (4, 'eiffel.jpeg', 'jpeg', 'Eiffel Tower', '2018-07-04');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (5, 'nantucketlighthouse.jpeg', 'jpeg', 'Nantucket', '2017-11-22');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (8, 'praguestreets.jpeg', 'jpeg', 'Streets of Prague', '2018-06-20');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (7, 'exeterflowers.jpeg', 'jpeg', 'Flowers of Exeter, NH ', '2018-05-14');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (6, 'tokyotower.jpeg', 'jpeg', 'Tokyo Tower', '2018-12-14');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (9, 'kaelawinter.jpeg', 'jpeg', 'Kaela at Cornell', '2020-01-20');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (10, 'portlandlighthouse.jpeg', 'jpeg', 'Portland, Maine', '2017-10-17');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (11, 'losangelesdrives.jpeg', 'jpeg', 'Los Angeles Sunset Drives', '2019-12-31');
/**Source: (original work) Sophia Oguri*/
INSERT INTO images (id, file_name, file_ext, description, date) VALUES (12, 'utetrail.jpeg', 'jpeg', 'Ute Trail in Aspen, CO', '2020-7-31');

INSERT INTO tags (id, keyword) VALUES (1, 'amsterdam');
INSERT INTO tags (id, keyword) VALUES (2, 'flowers');
INSERT INTO tags (id, keyword) VALUES (3, 'ithaca');
INSERT INTO tags (id, keyword) VALUES (4, 'paris');
INSERT INTO tags (id, keyword) VALUES (5, 'summer');
INSERT INTO tags (id, keyword) VALUES (6, 'nantucket');
INSERT INTO tags (id, keyword) VALUES (7, 'winter');
INSERT INTO tags (id, keyword) VALUES (8, 'prague');
INSERT INTO tags (id, keyword) VALUES (9, 'exeter');
INSERT INTO tags (id, keyword) VALUES (10, 'tokyo');
INSERT INTO tags (id, keyword) VALUES (11, 'newengland');
INSERT INTO tags (id, keyword) VALUES (12, 'portland');
INSERT INTO tags (id, keyword) VALUES (13, 'losangeles');

INSERT INTO image_tags (id, image_id, tag_id) VALUES (1, 1, 1);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (2, 1, 5);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (3, 2, 1);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (4, 2, 2);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (5, 2, 5);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (6, 3, 3);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (7, 3, 5);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (8, 4, 4);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (9, 4, 5);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (10, 5, 6);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (11, 5, 11);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (12, 8, 8);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (13, 7, 9);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (14, 7, 11);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (15, 7, 2);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (16, 6, 10);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (17, 9, 3);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (18, 9, 7);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (19, 10, 11);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (20, 10, 12);
INSERT INTO image_tags (id, image_id, tag_id) VALUES (21, 11, 13);

COMMIT;
