-- Create tables based on the ER diagram
DROP DATABASE IF EXISTS recipehub_db;
CREATE DATABASE recipehub_db;
USE recipehub_db;
-- Users table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Recipe table
CREATE TABLE Recipe (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    image_url VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Post table
CREATE TABLE Post (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    user_id INT,
    content TEXT NOT NULL,
    title VARCHAR(100) NOT NULL,
    creation_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES Recipe(recipe_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id)
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

-- Recipes with detailed step-by-step instructions
INSERT INTO Recipe (user_id, title, description, creation_datetime, image_url) VALUES 
(1, 'Classic Chocolate Chip Cookies', 
'1. Preheat oven to 375°F (190°C).
2. In a large bowl, cream together butter, white sugar, and brown sugar until smooth.
3. Beat in eggs one at a time, then stir in vanilla.
4. Dissolve baking soda in hot water and add to batter along with salt.
5. Stir in flour and chocolate chips.
6. Drop by large spoonfuls onto ungreased pans.
7. Bake for about 10 minutes or until edges are nicely browned.
8. Let cool on baking sheet for 5 minutes before transferring to a wire rack.', 
'2024-02-15 14:30:00', 'cookies.jpg'),

(2, 'Homemade Margherita Pizza', 
'1. Prepare pizza dough and let it rise for 1-2 hours.
2. Preheat oven to 475°F (245°C) with pizza stone inside.
3. Roll out dough on floured surface to desired thickness.
4. Spread tomato sauce evenly over dough, leaving a small border for the crust.
5. Arrange fresh mozzarella slices over sauce.
6. Bake for 10-12 minutes until crust is golden and cheese is bubbly.
7. Remove from oven and immediately top with fresh basil leaves.
8. Drizzle with olive oil and sprinkle with salt before serving.', 
'2024-01-20 18:45:00', 'pizza.jpg'),

(3, 'Creamy Mushroom Risotto', 
'1. Heat broth in a saucepan and keep warm over low heat.
2. In a large pot, heat olive oil and sauté onions until translucent.
3. Add mushrooms and cook until they release their moisture and brown.
4. Add arborio rice and stir to coat with oil.
5. Add white wine and cook until absorbed.
6. Add warm broth 1/2 cup at a time, stirring continuously and waiting until liquid is absorbed before adding more.
7. Continue process for about 20 minutes until rice is creamy and al dente.
8. Remove from heat and stir in butter and parmesan cheese.
9. Season with salt and pepper to taste.
10. Let stand for 2 minutes before serving.', 
'2024-03-05 19:15:00', 'risotto.jpg'),

(4, 'Fresh Summer Salad', 
'1. Wash and dry all vegetables thoroughly.
2. Chop lettuce, cucumber, bell peppers, and cherry tomatoes.
3. Slice red onion thinly and soak in cold water for 10 minutes to reduce sharpness.
4. In a small bowl, whisk together olive oil, lemon juice, dijon mustard, honey, salt and pepper.
5. Drain onions and combine all vegetables in a large bowl.
6. Pour dressing over salad just before serving and toss gently.
7. Top with crumbled feta cheese and toasted pine nuts.', 
'2024-06-10 12:00:00', 'salad.jpg'),

(5, 'Slow Cooker Beef Stew', 
'1. Cut beef into 1-inch cubes and season with salt and pepper.
2. Heat oil in a large skillet and brown meat on all sides.
3. Transfer meat to slow cooker.
4. In the same skillet, sauté onions and garlic until soft.
5. Add flour and tomato paste, stirring to combine.
6. Gradually stir in beef broth and red wine, scraping up browned bits from pan.
7. Pour mixture over beef in slow cooker.
8. Add carrots, potatoes, celery, bay leaves, thyme, and rosemary.
9. Cover and cook on low for 7-8 hours or on high for 4 hours.
10. In the last 30 minutes, stir in frozen peas.
11. Remove bay leaves before serving.', 
'2024-04-22 09:30:00', 'beef_stew.jpg');

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
(1, 2, 'This pizza dough recipe is incredible. I\'ve been struggling with getting the right texture until now. Will definitely make again!', '2024-01-22 19:30:00'),
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