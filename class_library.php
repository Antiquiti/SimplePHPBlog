<?php

class Database
{
    private $connection;
    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";

    public static function connectToDB($database = "blog")
    {
        $instance = new self();
        $instance->connection = mysqli_connect($instance->host,$instance->user,$instance->password,$database);
        return $instance;
    }

    public function query($query)
    {
        mysqli_query($this->connection,"SET NAMES utf8");
        return mysqli_query($this->connection,$query);
    }

    public function insert($query)
    {
        $this->query($query);
        return mysqli_insert_id($this->connection);
    }

    public function select($query)
    {
        $new_array = [];
        $result = $this->query($query);
        while($row = mysqli_fetch_assoc($result))
        {
            $new_array[] = $row;
        }
        return $new_array;
    }
}

class Category
{
    private $id;
    private $name;
    private $slug;

    public function __construct($name)
    {
        $this->name = $name;
        $this->slug = $this->makeSlug($this->name);
        return $this;
    }

    private function makeSlug($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        if (empty($text)) 
        {
            return 'n-a';
        }

        return $text;
    }

    public function save(Database $db)
    {
        $query = "INSERT INTO categories (name,slug) VALUES ('$this->name','$this->slug')";
        $id = $db->insert($query);
        $this->id = $id;
        return $this;
    }

    public static function getCategories(Database $db)
    {
        $query = "SELECT * FROM categories";
        return $db->select($query);
    }

    public static function getCategory($id)
    {
        $db = Database::connectToDB();
        $query = "SELECT * from categories WHERE id = '$id'";
        $categories = $db->select($query);
        return count($categories) > 0 ? $categories[0] : null;
    }
}

class Post
{
    private $id;
    private $title;
    private $content;
    private $creation_date;
    private $update_date;

    public function __construct(array $attributes = [])
    {
        /*
            [ $attributes
                $key    => $value
                'title' => 'test',
                'id' => 1
            ],
        */
        foreach($attributes as $key=>$value)
        {
            $this->$key = $value;
        }

        return $this;
    }

    public function save(Database $db)
    {
        $this->creation_date = date('Y-m-d H:i:s');
        $this->update_date = $this->creation_date;
        $query = "INSERT INTO posts (title,content,creation_date,update_date) VALUES ('$this->title','$this->content','$this->creation_date','$this->update_date')";
        $id = $db->insert($query);
        $this->id = $id;
        return $this;
    }

    public function getAttribute($name)
    {
        return $this->$name;
    }

    public function assignCategory($id, Database $db)
    {
        $query = "INSERT INTO post2category (category_id,post_id) VALUES ('$id','$this->id')";
        $db->insert($query);
    }

    public function rebuildRelation($post_id, $cat_id, Database $db)
    {
        $query = "DELETE FROM post2category WHERE post_id='$post_id'";
        $db->insert($query);
        $this->assignCategory($cat_id, $db);
    }

    public function updateCategory($cat_id, $post_id, Database $db)
    {
        $query = "UPDATE post2category set category_id = '$cat_id' WHERE post_id = '$post_id'";
        $db->insert($query);
    }

    public static function getPosts(Database $db)
    {
        $query = "SELECT * FROM posts";
        $result = $db->select($query);
        return self::buildCollection($result);
    }

    public static function getLimitedPosts(Database $db, $start, $numberOfElements)
    {
        $query = "SELECT * FROM posts LIMIT $start, $numberOfElements";
        $result = $db->select($query);
        return self::buildCollection($result);
    }

    public static function countPosts(Database $db)
    {
        $result = $db->query("SELECT count(*) as 'count' from posts")->fetch_array();
        return $result[0];
    }

    public static function buildCollection(array $data)
    {
        $array = [];
        foreach($data as $post_data)
        {
            $array[] = new Post($post_data);
        }
        return $array;
    }

    public static function getPostsByCategory(Database $db, $id)
    {
        $queryPivot = "SELECT * FROM post2category WHERE category_id=$id";
        $post2category = $db->select($queryPivot);
        $post_ids = array_map(function($item)
        {
            return $item["post_id"];
        },$post2category);
        $queryPosts = "SELECT * from posts WHERE id in(".implode(",",$post_ids).")";
        $result = $db->select($queryPosts);
        return self::buildCollection($result);
    }

    public static function getPost($id)
    {
        $db = Database::connectToDB();
        $query = "SELECT * from posts WHERE id = '$id'";
        $posts = $db->select($query);
        return count($posts) > 0 ? new Post($posts[0]) : null;
    }

    public function editDB($content, $title, $date)
    {
        $db = Database::connectToDB();
        $query = "UPDATE posts set content='$content', title='$title', update_date='$date' WHERE id= $this->id";
        $db->insert($query);
    }

    public static function getCategory($id)
    {
        $db = Database::connectToDB();
        $query = "SELECT category_id FROM post2category where post_id = $id";
        $cat_id = $db->select($query);
        return count($cat_id) > 0 ? $cat_id[0] : null;
    }

    public function getExcerpt()
    {
        $words = explode(" ", $this->content);
        $limitedWordsArray = array_slice($words,0,10);
        $limitedWords = implode(" ",$limitedWordsArray);
        if(count($limitedWordsArray) != count($words))
        {
            $limitedWords .= "...";
        }
        return $limitedWords;
    }

}