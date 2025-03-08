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

-- Insert Dummy Data

-- Cuisines
INSERT INTO Cuisine (cuisine_name) VALUES ('Italian'), ('Chinese'), ('Mexican'), ('Indian'), ('American');

-- Categories
INSERT INTO Category (category_name) VALUES ('Appetizer'), ('Main Course'), ('Dessert'), ('Beverage'), ('Snack');

-- Recipes
INSERT INTO Recipe (user_id, cuisine_id, category_id, recipe_name, title, prep_time, cook_time, servings, spicy, image_url)
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
