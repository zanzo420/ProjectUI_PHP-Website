SHOW TABLES;

SHOW COLUMNS FROM table_name;

SELECT * FROM table_name;

$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

		// print contents of result:
		while($row = mysqli_fetch_row($result)){
			var_dump($row);
			echo "<hr />";
		}
		die("");


ALTER TABLE sub_nav_items ADD link TEXT;