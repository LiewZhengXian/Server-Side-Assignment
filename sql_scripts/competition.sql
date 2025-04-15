-- Table for competitions
-- This table stores information about various competitions
CREATE TABLE competition (
    competition_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL DEFAULT NULL
);

-- Table for competition submissions
-- This table tracks submissions made by users for each competition
CREATE TABLE competition_submission (
    submission_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_id INT NOT NULL,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    submission_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (competition_id) REFERENCES competition(competition_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE
);

-- Table for competition votes
-- This table tracks votes for each submission in a competition
CREATE TABLE competition_vote (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    submission_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES competition_submission(submission_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

-- Table for competition results
-- This table stores the results of each competition, including ranks and prizes
CREATE TABLE competition_result (
    result_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_id INT NOT NULL,
    submission_id INT NOT NULL,
    rank INT NOT NULL,
    prize VARCHAR(255) NOT NULL,
    FOREIGN KEY (competition_id) REFERENCES competition(competition_id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES competition_submission(submission_id) ON DELETE CASCADE
);

-- Dummy data insertion
-- Competitions
INSERT INTO competition (competition_name, image_path, description, start_date, end_date)
VALUES
    -- Ongoing Competition
    ('MasterChef Malaysia 2025', 'https://upload.wikimedia.org/wikipedia/ms/3/36/Masterchef_msia.jpg', 'An intense cook-off among Malaysia''s top amateur chefs.', '2025-03-28 10:00:00', '2025-04-05 18:00:00'),

    -- Upcoming Competitions
    ('Ramadhan Recipe Challenge', 'https://images.squarespace-cdn.com/content/v1/5648788be4b036fed226dece/1501943615969-CTZN7RVUSQ5C06E9WTFZ/Kit%27s+Article+image.jpg?format=2500w', 'Create the best traditional dishes for the Ramadhan season.', '2025-04-10 09:00:00', '2025-04-20 17:00:00'),
    ('Merdeka Home Cook Showdown', 'https://storage.googleapis.com/buro-malaysia-storage/www.buro247.my/2024/08/ff08f05b-concorde.jpg', 'A nationwide cooking competition to celebrate Malaysia Day.', '2025-08-20 10:00:00', '2025-08-31 20:00:00'),

    -- Completed Competitions
    ('Chinese New Year Cook-Off', 'https://images.ctfassets.net/h4s2feya909e/24dETTLDhAshSGhoJFxAmH/135a363060560348a1e5b9773f4ca363/chinese-new-year-food-feast.jpg?w=3840&h=2560&fit=fill&q=60&fm=webp', 'Participants showcased festive Chinese dishes.', '2025-01-10 08:00:00', '2025-01-20 18:00:00'),
    ('Valentine''s Dessert Duel', 'https://foxeslovelemons.com/wp-content/uploads/2022/02/Valentine-Dessert-Foxes-Love-Lemons.jpg', 'Chefs competed to create the most romantic dessert.', '2025-02-10 10:00:00', '2025-02-14 15:00:00');

-- Insert dummy data into competition_submission
INSERT INTO competition_submission (competition_id, user_id, recipe_id, submission_date) VALUES
(1, 2, 4, '2025-03-28 12:00:00'),
(2, 4, 2, '2025-03-28 14:00:00'),
(4, 5, 5, '2025-04-11 10:30:00'),
(4, 1, 3, '2025-04-12 16:15:00'),
(5, 3, 1, '2025-08-21 09:45:00');

-- Insert dummy data into competition_vote
INSERT INTO competition_vote (submission_id, user_id, vote_date) VALUES
(1, 3, '2025-03-29 10:00:00'),
(1, 1, '2025-03-29 11:30:00'),
(3, 4, '2025-03-30 14:00:00'),
(3, 2, '2025-04-12 15:20:00'),
(5, 1, '2025-04-13 17:45:00'),
(2, 5, '2025-04-05 22:30:00'),
(1, 5, '2025-04-08 16:20:00');

-- Insert dummy data into competition_result
INSERT INTO competition_result (competition_id, submission_id, rank, prize) VALUES
(1, 1, 1, 'RM5000 Cash'),
(2, 1, 2, 'RM3000 Cash'),
(3, 4, 3, 'RM4000 Cash'),
(4, 4, 4, 'RM2000 Cash'),
(5, 5, 1, 'RM6000 Cash');
