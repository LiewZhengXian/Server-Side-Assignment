DROP DATABASE IF EXISTS recipehub_db;
CREATE DATABASE recipehub_db;
USE recipehub_db;

-- Drop tables if they exist to avoid conflicts
DROP TABLE IF EXISTS Rating;
DROP TABLE IF EXISTS Recipe;
DROP TABLE IF EXISTS Comment;
DROP TABLE IF EXISTS Post;
DROP TABLE IF EXISTS User;

-- Create User table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create Post table
CREATE TABLE Post (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Comment table
CREATE TABLE Comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Recipe table (added image_url column)
CREATE TABLE Recipe (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500),  -- New column for storing image URL
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES Post(post_id) ON DELETE CASCADE
);

-- Create Rating table
CREATE TABLE Rating (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    rating_value INT CHECK (rating_value BETWEEN 1 AND 5),
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Establishing Foreign Key relationships in User table
ALTER TABLE User 
ADD COLUMN post_id INT,
ADD COLUMN recipe_id INT,
ADD COLUMN comment_id INT,
ADD FOREIGN KEY (post_id) REFERENCES Post(post_id) ON DELETE SET NULL,
ADD FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE SET NULL,
ADD FOREIGN KEY (comment_id) REFERENCES Comment(comment_id) ON DELETE SET NULL;

-- Establishing Foreign Key in Post table
ALTER TABLE Post 
ADD COLUMN comment_id INT,
ADD FOREIGN KEY (comment_id) REFERENCES Comment(comment_id) ON DELETE SET NULL;

-- Insert into User table
INSERT INTO User (username, email, password) VALUES
('Alice', 'alice@example.com', 'password123'),
('Bob', 'bob@example.com', 'securepass'),
('Charlie', 'charlie@example.com', 'charliepass'),
('David', 'david@example.com', 'david123'),
('Eve', 'eve@example.com', 'evepass');

-- Insert into Post table
INSERT INTO Post (content, title) VALUES
('This is my first recipe post!', 'Delicious Chocolate Cake'),
('Healthy eating tips for students.', 'Nutrition Tips'),
('The best homemade pizza recipe.', 'Pizza Perfection'),
('How to make sushi at home.', 'Sushi Making Guide'),
('A guide to different coffee brewing methods.', 'Coffee Lovers Special');

-- Insert into Comment table
INSERT INTO Comment (content) VALUES
('Great recipe! I love it.'),
('Thanks for the nutrition tips! Very helpful.'),
('I tried this pizza recipe, and it was amazing!'),
('Sushi is my favorite, can’t wait to try this.'),
('Awesome coffee guide, I learned a lot.');

-- Insert into Recipe table (linking posts to recipes with image URLs)
INSERT INTO Recipe (post_id, title, description, image_url) VALUES
(1, 'Chocolate Cake', '1. Preheat the oven to 350°F (175°C).
2. Grease and flour a cake pan.
3. In a bowl, mix 2 cups of flour, 1 and 3/4 cups of sugar, 3/4 cup of cocoa powder, 1 and 1/2 teaspoons of baking powder, 1 and 1/2 teaspoons of baking soda, and 1 teaspoon of salt.
4. Add 2 eggs, 1 cup of milk, 1/2 cup of vegetable oil, and 2 teaspoons of vanilla extract. Mix well.
5 .Stir in 1 cup of boiling water until the batter is smooth.
6 .Pour the batter into the prepared cake pan.
7. Bake for 30 to 35 minutes or until a toothpick inserted in the center comes out clean.
8. Let the cake cool in the pan for 10 minutes, then transfer to a wire rack to cool completely.
9. Frost with your favorite chocolate frosting and serve.', ''),
(3, 'Homemade Pizza', 'Step-by-step guide to making perfect homemade pizza.', ''),
(4, 'Sushi Rolls', 'Learn how to make sushi rolls with simple ingredients.', ''),
(5, 'Coffee Brewing', 'Discover different ways to brew coffee.', ''),
(2, 'Healthy Eating', 'A detailed guide to balanced meals.', '');

-- Insert into Rating table (linking recipes to ratings)
INSERT INTO Rating (recipe_id, rating_value) VALUES
(1, 5),
(2, 4),
(3, 5),
(4, 3),
(5, 4);

