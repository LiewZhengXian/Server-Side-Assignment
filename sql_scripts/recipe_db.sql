-- Combined SQL Script for Recipe Hub Database

-- Drop and Create Database
DROP DATABASE IF EXISTS recipehub_db;
CREATE DATABASE recipehub_db;
USE recipehub_db;

-- Password Reset Table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN DEFAULT 0
);

-- Cuisine Table
CREATE TABLE Cuisine (
    cuisine_id INT AUTO_INCREMENT PRIMARY KEY,
    cuisine_name VARCHAR(50) NOT NULL
);

-- Category Table
CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

-- Recipe Table
CREATE TABLE Recipe (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cuisine_id INT,
    category_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prep_time TIME NOT NULL,
    cook_time TIME NOT NULL,
    servings INT NOT NULL,
    spicy BOOLEAN NOT NULL DEFAULT FALSE,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (cuisine_id) REFERENCES Cuisine(cuisine_id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE SET NULL
);

-- Ingredient Table
CREATE TABLE Ingredient (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_name VARCHAR(100) NOT NULL
);

-- Recipe_Ingredient Table
CREATE TABLE Recipe_Ingredient (
    recipe_id INT,
    ingredient_id INT,
    quantity DECIMAL(10,2) NOT NULL,
    units VARCHAR(20) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES Ingredient(ingredient_id) ON DELETE CASCADE
);

-- Step Table
CREATE TABLE Step (
    step_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    step_num INT NOT NULL,
    instruction TEXT NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Post Table
CREATE TABLE Post (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    user_id INT,
    content TEXT NOT NULL,
    title VARCHAR(100) NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
);

-- Comment Table
CREATE TABLE Comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    content TEXT NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (post_id) REFERENCES Post(post_id) ON DELETE CASCADE
);

-- Rating Table
CREATE TABLE Rating (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    rating_value INT NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (post_id) REFERENCES Post(post_id) ON DELETE CASCADE
);

-- Competition Table
CREATE TABLE competition (
    competition_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL DEFAULT NULL
);

-- Competition Submission Table
CREATE TABLE competition_submission (
    submission_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_id INT NOT NULL,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    submission_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (competition_id) REFERENCES competition(competition_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Competition Vote Table
CREATE TABLE competition_vote (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    submission_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES competition_submission(submission_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
);

-- Competition Result Table
CREATE TABLE competition_result (
    result_id INT PRIMARY KEY AUTO_INCREMENT,
    competition_id INT NOT NULL,
    submission_id INT NOT NULL,
    rank INT NOT NULL,
    prize VARCHAR(255) NOT NULL,
    FOREIGN KEY (competition_id) REFERENCES competition(competition_id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES competition_submission(submission_id) ON DELETE CASCADE
);

-- Meal Plans Table
CREATE TABLE meal_plans (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    meal_date DATE NOT NULL,
    meal_time ENUM('Breakfast','Lunch','Dinner') NOT NULL,
    meal_type ENUM('recipe','custom') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    recipe_id INT DEFAULT NULL,
    custom_meal VARCHAR(255) DEFAULT NULL,
    duration INT NOT NULL DEFAULT 1 COMMENT 'Number of days this meal is planned for',
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Meal Template Table
CREATE TABLE meal_template (
    template_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
);

-- Meal Template Details Table
CREATE TABLE meal_template_details (
    template_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    meal_time ENUM('Breakfast','Lunch','Dinner') NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    meal_type VARCHAR(255) NOT NULL,
    recipe_id INT DEFAULT NULL,
    custom_meal VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (template_id) REFERENCES meal_template(template_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Dummy Data Insertion

-- Users
INSERT INTO User (username, email, password, isAdmin) VALUES 
('john_doe', 'john@example.com', '$2y$10$9XmxGHZZFVQ8zUFi1ou8W.HB/4QuOgbSZ1Fc1e6F9U5dHpZtUEIl2', 0),
('jane_smith', 'jane@example.com', '$2y$10$wKIHdBw3t76uGxhG8BMeEeSqGCPJHd9fHUbd71LbA.yLtHGhOVkPa', 0),
('chef_mike', 'mike@cookingpro.com', '$2y$10$GtERfCHrxCOcfujO5Vwbteq6gzCBIHyqWmGsKEYCUYiGkYNFCIp6a', 0),
('foodie_lisa', 'lisa@foodblog.com', '$2y$10$6aXvPlKPzEXHBsY4LDaQc.Nkch3xIX.7MWdrXMXg5XNvuJMD3PnKO', 0),
('cooking_dad', 'dad@familyrecipes.com', '$2y$10$Z7kUBRDg3cLVx2fvwQvVKOIlCd0jCQH9bIEEYAVi3/WJHmcniPNJ2', 0),
('test1', 'test1@gmail.com', '5a105e8b9d40e1329780d62ea2265d8a', 0),
('admin1', 'admin1@gmail.com', 'e00cf25ad42683b3df678c61f42c6bda', 1),
('gordon_ramsey', 'gordon_ramsey@flavorvault.org', MD5('abcd12341'), 0),
('uncle_roger', 'uncle_roger@flavorvault.org', MD5('abcd12342'), 0),
('cathy_liu', 'cathy_liu@tastybite.net', MD5('abcd12343'), 0),
('daniel_ross', 'daniel_ross@flavorvault.org', MD5('abcd12344'), 0),
('emily_tan', 'emily_tan@foodlover.net', MD5('abcd12345'), 0),
('felix_ng', 'felix_ng@tastybite.net', MD5('abcd12346'), 0),
('grace_cho', 'grace_cho@recipemail.info', MD5('abcd12347'), 0),
('harry_lee', 'harry_lee@recipemail.info', MD5('abcd12348'), 0),
('ivy_lim', 'ivy_lim@bakersmail.com', MD5('abcd12349'), 0),
('jackson_yap', 'jackson_yap@bakersmail.com', MD5('abcd123410'), 0),
('karen_ong', 'karen_ong@foodlover.net', MD5('abcd123411'), 0),
('leon_ho', 'leon_ho@yumrecipes.io', MD5('abcd123412'), 0),
('michelle_foo', 'michelle_foo@mailme.org', MD5('abcd123413'), 0),
('nathan_wong', 'nathan_wong@bakersmail.com', MD5('abcd123414'), 0),
('olivia_teh', 'olivia_teh@yumrecipes.io', MD5('abcd123415'), 0),
('peter_chin', 'peter_chin@bakersmail.com', MD5('abcd123416'), 0),
('quincy_lam', 'quincy_lam@cookmail.com', MD5('abcd123417'), 0),
('rachel_toh', 'rachel_toh@flavorvault.org', MD5('abcd123418'), 0),
('samuel_khoo', 'samuel_khoo@yumrecipes.io', MD5('abcd123419'), 0),
('tina_loke', 'tina_loke@cookmail.com', MD5('abcd123420'), 0);


-- Cuisines
INSERT INTO Cuisine (cuisine_name) VALUES ('Italian'), ('Chinese'), ('Mexican'), ('Indian'), ('American');

-- Categories
INSERT INTO Category (category_name) VALUES ('Appetizer'), ('Main Course'), ('Dessert'), ('Beverage'), ('Snack');

-- Recipes
INSERT INTO Recipe (user_id, cuisine_id, category_id, title, description, prep_time, cook_time, servings, spicy, image_path)
VALUES
(1, 1, 2, 'Spaghetti Carbonara', 'Classic Italian pasta dish with creamy sauce', '00:15:00', '00:20:00', 2, FALSE, '../recipe_management_module/recipe_img/sample_food.jpg'),
(2, 2, 2, 'Kung Pao Chicken', 'A spicy, stir-fried Chinese dish with peanuts', '00:10:00', '00:15:00', 4, TRUE, '../recipe_management_module/recipe_img/sample_food.jpg'),
(3, 3, 2, 'Tacos al Pastor', 'Traditional Mexican tacos with marinated pork', '00:20:00', '00:30:00', 3, TRUE, '../recipe_management_module/recipe_img/sample_food.jpg'),
(4, 4, 2, 'Butter Chicken', 'Creamy and flavorful Indian chicken dish', '00:25:00', '00:40:00', 4, TRUE, '../recipe_management_module/recipe_img/sample_food.jpg'),
(5, 5, 3, 'Chocolate Brownie', 'Rich and fudgy American chocolate dessert', '00:10:00', '00:25:00', 6, FALSE, '../recipe_management_module/recipe_img/sample_food.jpg');

-- Ingredients
INSERT INTO Ingredient (ingredient_name) VALUES ('Pasta'), ('Chicken'), ('Peanuts'), ('Pork'), ('Butter'), ('Chocolate');

-- Recipe Ingredients
INSERT INTO Recipe_Ingredient (recipe_id, ingredient_id, quantity, units)
VALUES
(1, 1, 200, 'grams'),
(2, 2, 300, 'grams'),
(2, 3, 50, 'grams'),
(3, 4, 400, 'grams'),
(4, 5, 100, 'grams'),
(5, 6, 150, 'grams');

-- Steps
INSERT INTO Step (recipe_id, step_num, instruction)
VALUES
(1, 1, 'Boil pasta in salted water for 10 minutes'),
(1, 2, 'Mix with sauce and serve hot'),
(2, 1, 'Stir-fry chicken with peanuts and spices'),
(3, 1, 'Grill marinated pork and serve in tacos'),
(4, 1, 'Cook chicken in butter and tomato sauce'),
(5, 1, 'Bake chocolate mixture in preheated oven for 25 minutes');

-- Posts
INSERT INTO Post (recipe_id, user_id, content, title, creation_datetime) VALUES 
(1, 1, 'These chocolate chip cookies are a family favorite that I\'ve been perfecting for years. The secret is using both white and brown sugar for the perfect texture and flavor. Make sure you don\'t overbake them - they should still be slightly soft in the center when you take them out of the oven.', 'The Perfect Chocolate Chip Cookie Recipe', '2024-02-16 10:25:00'),
(2, 2, 'After traveling to Naples, I was inspired to recreate the authentic margherita pizza at home. The key is using simple, high-quality ingredients: San Marzano tomatoes, fresh mozzarella, and basil from my garden. This recipe comes close to the real Italian experience!', 'Authentic Neapolitan Pizza at Home', '2024-01-21 14:10:00'),
(3, 3, 'Risotto is often seen as difficult, but it\'s really about patience and attention. This mushroom risotto is rich, creamy, and full of umami flavor. I prefer using a mix of wild mushrooms when they\'re in season. Pair with a glass of the same white wine you use in the recipe!', 'Master the Art of Perfect Risotto', '2024-03-07 20:30:00'),
(4, 4, 'When summer vegetables are at their peak, this simple salad lets their natural flavors shine. I love bringing this to potlucks and barbecues. The dressing can be made ahead, but don\'t add it until just before serving to keep everything crisp and fresh.', 'Summer Farmers Market Salad', '2024-06-12 11:45:00'),
(5, 5, 'This hearty beef stew is perfect for cold winter days. The slow cooker does all the work, filling your home with amazing aromas all day. I like to serve it with crusty bread for soaking up the rich gravy. Leftovers taste even better the next day!', 'Comfort Food: Grandma\'s Beef Stew Recipe', '2024-04-23 17:15:00');

-- Comments
INSERT INTO Comment (user_id, post_id, content, creation_datetime) VALUES 
(2, 1, 'I tried these cookies last night and they were amazing! My kids ate them all in one sitting. I added some walnuts for extra crunch.', '2024-02-17 15:20:00'),
(3, 1, 'Great recipe! I found that chilling the dough for an hour before baking made them even better. Thanks for sharing!', '2024-02-18 09:45:00'),
(1, 2, 'This pizza dough recipe is incredible. I''ve been struggling with getting the right texture until now. Will definitely make again!', '2024-01-22 19:30:00'),
(4, 3, 'I never thought I could make restaurant-quality risotto at home, but your detailed instructions made it foolproof. Delicious!', '2024-03-10 18:15:00'),
(5, 4, 'Added some grilled chicken to make this a complete meal. The dressing is perfect - not too heavy but full of flavor.', '2024-06-15 13:40:00');

-- Ratings
INSERT INTO Rating (user_id, post_id, rating_value) VALUES 
(2, 1, 5),
(3, 1, 4),
(4, 1, 5),
(1, 2, 5),
(5, 2, 4),
(2, 3, 4),
(4, 3, 5),
(1, 4, 3),
(3, 4, 4),
(2, 5, 5);

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
-- For each competition (1-5), create multiple submissions from users 8-25
-- Note: Competition 3 is upcoming, so there are no submissions for it
INSERT INTO competition_submission (competition_id, user_id, recipe_id, submission_date) VALUES
-- Competition 1 submissions (completed)
(1, 8, 3, '2025-02-15 09:12:34'),  -- submission_id = 6
(1, 11, 2, '2025-02-17 14:23:45'), -- submission_id = 7
(1, 15, 5, '2025-02-18 10:45:12'), -- submission_id = 8
(1, 19, 1, '2025-02-18 16:30:00'), -- submission_id = 9
(1, 23, 4, '2025-02-19 11:22:33'), -- submission_id = 10

-- Competition 2 submissions (ongoing)
(2, 9, 1, '2025-03-05 08:15:00'),   -- submission_id = 11
(2, 12, 4, '2025-03-06 13:40:22'),  -- submission_id = 12
(2, 16, 3, '2025-03-07 17:25:14'),  -- submission_id = 13
(2, 20, 5, '2025-03-08 09:52:11'),  -- submission_id = 14
(2, 24, 2, '2025-03-08 15:18:40'),  -- submission_id = 15
(2, 10, 1, '2025-03-08 23:59:59'),  -- submission_id = 16

-- Competition 4 submissions (completed)
(4, 10, 4, '2025-04-08 08:30:00'),  -- submission_id = 17
(4, 14, 1, '2025-04-09 11:45:22'),  -- submission_id = 18
(4, 18, 5, '2025-04-10 15:20:17'),  -- submission_id = 19
(4, 22, 2, '2025-04-11 09:12:34'),  -- submission_id = 20
(4, 8, 3, '2025-04-11 17:30:00'),   -- submission_id = 21
(4, 12, 4, '2025-04-12 10:05:55'),  -- submission_id = 22
(4, 16, 1, '2025-04-12 14:22:38'),  -- submission_id = 23

-- Competition 5 submissions (completed)
(5, 9, 2, '2025-07-15 09:00:00'),   -- submission_id = 24
(5, 13, 5, '2025-07-16 12:34:56'),  -- submission_id = 25
(5, 17, 1, '2025-07-17 14:15:27'),  -- submission_id = 26
(5, 21, 3, '2025-07-18 16:38:42'),  -- submission_id = 27
(5, 25, 4, '2025-07-19 10:20:30'),  -- submission_id = 28
(5, 11, 2, '2025-07-19 15:45:19'),  -- submission_id = 29
(5, 15, 5, '2025-07-20 08:55:12'),  -- submission_id = 30
(5, 19, 1, '2025-07-20 13:27:43');  -- submission_id = 31

-- Insert dummy data into competition_vote
-- Users can vote for submissions they didn't create
-- Note: No votes for competition 3 as it's upcoming
INSERT INTO competition_vote (submission_id, user_id, vote_date) VALUES
-- Votes for Competition 1 submissions (completed)
(6, 9, '2025-02-20 10:15:00'),
(6, 10, '2025-02-20 11:22:33'),
(6, 12, '2025-02-21 09:30:45'),
(6, 13, '2025-02-21 14:55:21'),
(7, 10, '2025-02-22 12:05:15'),
(7, 14, '2025-02-22 18:20:37'),
(8, 9, '2025-02-23 08:45:00'),
(8, 12, '2025-02-23 13:15:29'),
(8, 13, '2025-02-24 10:30:42'),
(9, 8, '2025-02-24 15:10:18'),
(9, 11, '2025-02-25 09:55:33'),
(10, 9, '2025-02-25 14:20:05'),
(10, 14, '2025-02-26 11:35:48'),

-- Votes for Competition 2 submissions (ongoing)
(11, 8, '2025-03-09 09:10:00'),
(11, 11, '2025-03-09 12:22:15'),
(11, 13, '2025-03-10 14:05:38'),
(12, 10, '2025-03-10 16:30:42'),
(12, 14, '2025-03-11 08:45:19'),
(12, 15, '2025-03-11 13:18:27'),
(13, 9, '2025-03-12 10:25:33'),
(13, 11, '2025-03-12 15:40:51'),
(14, 8, '2025-03-13 09:15:08'),
(14, 13, '2025-03-13 14:30:22'),
(15, 10, '2025-03-14 11:20:37'),
(15, 12, '2025-03-14 16:45:59'),
(16, 13, '2025-04-15 09:05:10'),
(16, 15, '2025-04-15 13:25:48'),

-- Votes for Competition 4 submissions (completed)
(17, 9, '2025-04-13 09:30:00'),
(17, 11, '2025-04-13 14:45:15'),
(17, 13, '2025-04-14 10:15:27'),
(18, 8, '2025-04-14 15:30:39'),
(18, 12, '2025-04-15 11:45:52'),
(18, 15, '2025-04-15 17:00:18'),
(19, 9, '2025-04-16 08:20:33'),
(19, 14, '2025-04-16 13:40:45'),
(20, 11, '2025-04-17 09:55:57'),
(20, 13, '2025-04-17 15:10:12'),
(21, 8, '2025-04-18 11:25:24'),
(21, 15, '2025-04-18 16:40:36'),
(22, 9, '2025-04-19 08:55:48'),
(22, 12, '2025-04-19 14:10:03'),
(23, 11, '2025-04-20 10:25:15'),
(23, 14, '2025-04-20 15:40:27'),

-- Votes for Competition 5 submissions (completed)
(24, 8, '2025-07-21 10:00:00'),
(24, 10, '2025-07-21 15:15:22'),
(24, 12, '2025-07-22 11:30:33'),
(25, 14, '2025-07-22 16:45:45'),
(25, 16, '2025-07-23 08:00:57'),
(25, 18, '2025-07-23 13:15:09'),
(24, 20, '2025-07-24 09:30:21'),
(24, 22, '2025-07-24 14:45:33'),
(24, 24, '2025-07-25 10:00:45'),
(23, 8, '2025-07-25 15:15:57'),
(23, 12, '2025-07-26 11:30:09'),
(23, 16, '2025-07-26 16:45:21'),
(22, 20, '2025-07-27 08:00:33'),
(22, 24, '2025-07-27 13:15:45'),
(21, 10, '2025-07-28 09:30:57'),
(21, 14, '2025-07-28 14:45:09'),
(20, 18, '2025-07-29 10:00:21'),
(20, 22, '2025-07-29 15:15:33'),
(19, 8, '2025-07-30 11:30:45'),
(19, 16, '2025-07-30 16:45:57');

-- Insert dummy data into competition_result
-- Only top 3 rankings get prizes, others have NULL for prize
-- Note: No results for competition 2 (ongoing) or competition 3 (upcoming)
INSERT INTO competition_result (competition_id, submission_id, rank, prize) VALUES
-- Results for Competition 1 (completed)
(1, 8, 1, 'RM5000 Cash'),
(1, 10, 2, 'RM3000 Cash'),
(1, 7, 3, 'RM1500 Cash'),
(1, 9, 4, NULL),
(1, 6, 5, NULL),

-- Results for Competition 4 (completed)
(4, 19, 1, 'RM5500 Cash'),
(4, 23, 2, 'RM3200 Cash'),
(4, 17, 3, 'RM1800 Cash'),
(4, 21, 4, NULL),
(4, 20, 5, NULL),
(4, 18, 6, NULL),
(4, 22, 7, NULL),

-- Results for Competition 5 (completed)
(5, 25, 1, 'RM7000 Cash'),
(5, 10, 2, 'RM4000 Cash'),
(5, 13, 3, 'RM2500 Cash'),
(5, 14, 4, NULL),
(5, 17, 5, NULL),
(5, 21, 6, NULL),
(5, 25, 7, NULL),
(5, 19, 8, NULL);

-- Meal Plans
INSERT INTO meal_plans (meal_id, user_id, meal_name, meal_date, meal_time, meal_type, created_at, recipe_id, custom_meal, duration, updated_at) VALUES
(4, 6, 'Fish', '2025-03-28', 'Lunch', 'recipe', '2025-03-12 13:49:16', 4, NULL, 5, '2025-03-26 08:02:14'),
(5, 6, 'Bear', '2025-03-26', 'Dinner', 'custom', '2025-03-12 13:56:35', NULL, 'Bear and biscuit', 9, '2025-03-26 07:43:05'),
(6, 6, 'Fish and me ', '2025-04-12', 'Breakfast', 'recipe', '2025-04-12 13:54:04', 3, NULL, 9, NULL),
(7, 6, 'The cube', '2025-04-07', 'Lunch', 'custom', '2025-04-12 13:54:32', NULL, 'Eat rubic cube', 21, NULL);

-- Meal Templates
INSERT INTO meal_template (template_id, user_id, template_name, description, created_at, updated_at) VALUES
(4, 6, 'B', 'B', '2025-03-27 05:20:52', NULL),
(7, 6, 'Sushi mentai', 'Sushi mentai super good ', '2025-04-12 09:06:40', NULL);

-- Meal Template Details
INSERT INTO meal_template_details (template_detail_id, template_id, day_of_week, meal_time, meal_name, meal_type, recipe_id, custom_meal) VALUES
(85, 4, 'Monday', 'Breakfast', 'B', 'custom', NULL, 'dadawdaw'),
(86, 4, 'Monday', 'Lunch', 'B', 'recipe', 4, NULL),
(87, 4, 'Monday', 'Dinner', 'B', 'recipe', 2, NULL),
(88, 4, 'Tuesday', 'Breakfast', 'B', 'recipe', 2, NULL),
(89, 4, 'Tuesday', 'Lunch', 'B', 'recipe', 1, NULL),
(90, 4, 'Tuesday', 'Dinner', 'B', 'recipe', 1, NULL),
(91, 4, 'Wednesday', 'Breakfast', 'B', 'recipe', 4, NULL),
(92, 4, 'Wednesday', 'Lunch', 'B', 'recipe', 4, NULL),
(93, 4, 'Wednesday', 'Dinner', 'B', 'recipe', 2, NULL),
(94, 4, 'Thursday', 'Breakfast', 'B', 'recipe', 1, NULL),
(95, 4, 'Thursday', 'Lunch', 'B', 'recipe', 2, NULL),
(96, 4, 'Thursday', 'Dinner', 'B', 'recipe', 2, NULL),
(97, 4, 'Friday', 'Breakfast', 'B', 'recipe', 2, NULL),
(98, 4, 'Friday', 'Lunch', 'B', 'recipe', 3, NULL),
(99, 4, 'Friday', 'Dinner', 'B', 'recipe', 3, NULL),
(100, 4, 'Saturday', 'Breakfast', 'B', 'recipe', 2, NULL),
(101, 4, 'Saturday', 'Lunch', 'B', 'recipe', 2, NULL),
(102, 4, 'Saturday', 'Dinner', 'B', 'recipe', 3, NULL),
(103, 4, 'Sunday', 'Breakfast', 'B', 'recipe', 2, NULL),
(104, 4, 'Sunday', 'Lunch', 'B', 'recipe', 1, NULL),
(105, 4, 'Sunday', 'Dinner', 'B', 'recipe', 3, NULL),
(148, 7, 'Monday', 'Breakfast', 'A', 'recipe', 3, NULL),
(149, 7, 'Monday', 'Lunch', 'A', 'recipe', 1, NULL),
(150, 7, 'Monday', 'Dinner', 'A', 'recipe', 4, NULL),
(151, 7, 'Tuesday', 'Breakfast', 'A', 'recipe', 3, NULL),
(152, 7, 'Tuesday', 'Lunch', 'A', 'recipe', 3, NULL),
(153, 7, 'Tuesday', 'Dinner', 'A', 'recipe', 3, NULL),
(154, 7, 'Wednesday', 'Breakfast', 'A', 'recipe', 1, NULL),
(155, 7, 'Wednesday', 'Lunch', 'A', 'recipe', 2, NULL),
(156, 7, 'Wednesday', 'Dinner', 'A', 'recipe', 5, NULL),
(157, 7, 'Thursday', 'Breakfast', 'A', 'recipe', 1, NULL),
(158, 7, 'Thursday', 'Lunch', 'A', 'recipe', 3, NULL),
(159, 7, 'Thursday', 'Dinner', 'A', 'recipe', 4, NULL),
(160, 7, 'Friday', 'Breakfast', 'A', 'recipe', 3, NULL),
(161, 7, 'Friday', 'Lunch', 'A', 'recipe', 2, NULL),
(162, 7, 'Friday', 'Dinner', 'A', 'recipe', 3, NULL),
(163, 7, 'Saturday', 'Breakfast', 'A', 'recipe', 3, NULL),
(164, 7, 'Saturday', 'Lunch', 'A', 'recipe', 2, NULL),
(165, 7, 'Saturday', 'Dinner', 'A', 'recipe', 5, NULL),
(166, 7, 'Sunday', 'Breakfast', 'A', 'recipe', 4, NULL),
(167, 7, 'Sunday', 'Lunch', 'A', 'recipe', 4, NULL),
(168, 7, 'Sunday', 'Dinner', 'A', 'recipe', 3, NULL);