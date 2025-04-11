-- Create tables based on the ER diagram
DROP DATABASE IF EXISTS recipehub_db;
CREATE DATABASE recipehub_db;
USE recipehub_db;

-- Password reset table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Users table

CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Cuisine (
    cuisine_id INT AUTO_INCREMENT PRIMARY KEY,
    cuisine_name VARCHAR(50) NOT NULL
);

CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

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
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (cuisine_id) REFERENCES Cuisine(cuisine_id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE SET NULL
);

CREATE TABLE Ingredient (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_name VARCHAR(100) NOT NULL
);

CREATE TABLE Recipe_Ingredient (
    recipe_id INT,
    ingredient_id INT,
    quantity DECIMAL(10,2) NOT NULL,
    units VARCHAR(20) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES Ingredient(ingredient_id) ON DELETE CASCADE
);

CREATE TABLE Step (
    step_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    step_num INT NOT NULL,
    instruction TEXT NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id) ON DELETE CASCADE
);

-- Post table
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


-- Comment table
CREATE TABLE Comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    content TEXT NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (post_id) REFERENCES Post(post_id)
);

-- Rating table
CREATE TABLE Rating (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    rating_value INT NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (post_id) REFERENCES Post(post_id)
);



-- Insert dummy data
-- Users
INSERT INTO User (username, email, password) VALUES 
('john_doe', 'john@example.com', '$2y$10$9XmxGHZZFVQ8zUFi1ou8W.HB/4QuOgbSZ1Fc1e6F9U5dHpZtUEIl2'),
('jane_smith', 'jane@example.com', '$2y$10$wKIHdBw3t76uGxhG8BMeEeSqGCPJHd9fHUbd71LbA.yLtHGhOVkPa'),
('chef_mike', 'mike@cookingpro.com', '$2y$10$GtERfCHrxCOcfujO5Vwbteq6gzCBIHyqWmGsKEYCUYiGkYNFCIp6a'),
('foodie_lisa', 'lisa@foodblog.com', '$2y$10$6aXvPlKPzEXHBsY4LDaQc.Nkch3xIX.7MWdrXMXg5XNvuJMD3PnKO'),
('cooking_dad', 'dad@familyrecipes.com', '$2y$10$Z7kUBRDg3cLVx2fvwQvVKOIlCd0jCQH9bIEEYAVi3/WJHmcniPNJ2');

-- Insert Dummy Data

-- Cuisines
INSERT INTO Cuisine (cuisine_name) VALUES ('Italian'), ('Chinese'), ('Mexican'), ('Indian'), ('American');

-- Categories
INSERT INTO Category (category_name) VALUES ('Appetizer'), ('Main Course'), ('Dessert'), ('Beverage'), ('Snack');

-- Recipes
INSERT INTO Recipe (user_id, cuisine_id, category_id, title, description, prep_time, cook_time, servings, spicy, image_url)
VALUES
(1, 1, 2, 'Spaghetti Carbonara', 'Classic Italian pasta dish with creamy sauce', '00:15:00', '00:20:00', 2, FALSE, 'https://img.freepik.com/free-photo/top-view-table-full-delicious-food-composition_23-2149141352.jpg'),
(2, 2, 2, 'Kung Pao Chicken', 'A spicy, stir-fried Chinese dish with peanuts', '00:10:00', '00:15:00', 4, TRUE, 'https://img.freepik.com/free-photo/top-view-table-full-delicious-food-composition_23-2149141352.jpg'),
(3, 3, 2, 'Tacos al Pastor', 'Traditional Mexican tacos with marinated pork', '00:20:00', '00:30:00', 3, TRUE, 'https://img.freepik.com/free-photo/top-view-table-full-delicious-food-composition_23-2149141352.jpg'),
(4, 4, 2, 'Butter Chicken', 'Creamy and flavorful Indian chicken dish', '00:25:00', '00:40:00', 4, TRUE, 'https://img.freepik.com/free-photo/top-view-table-full-delicious-food-composition_23-2149141352.jpg'),
(5, 5, 3, 'Chocolate Brownie', 'Rich and fudgy American chocolate dessert', '00:10:00', '00:25:00', 6, FALSE, 'https://img.freepik.com/free-photo/top-view-table-full-delicious-food-composition_23-2149141352.jpg');

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