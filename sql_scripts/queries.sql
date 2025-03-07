SELECT c.content, u.username, c.creation_datetime 
FROM comment c 
JOIN user u ON c.comment_id = u.comment_id 
JOIN post p ON c.comment_id = p.comment_id 
WHERE p.post_id = 4 
ORDER BY c.creation_datetime DESC;