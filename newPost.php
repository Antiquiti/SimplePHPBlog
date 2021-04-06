<?php

        require("class_library.php");

        session_start();
        if(!empty($_SESSION['message'])) 
        {
            $message = $_SESSION['message'];
            echo "<script type='text/javascript'>alert('$message');</script>";
            $_SESSION['message'] = null;
        }

        $editpost = isset($_GET["editPost"]) ? Post::getPost($_GET["editPost"]) : new Post();
        $cat_id = isset($_GET["editPost"]) ? Post::getCategory($_GET["editPost"]) : null;
        $post2category = Category::getCategory($cat_id["category_id"]);

        if(isset($_POST["addPost"]))
        {        
            if(isset($_POST["id"]))
            {
                $edit_id = $_POST["selectCategory"];
                $editpost->rebuildRelation($_POST["id"],$edit_id, Database::connectToDB());
                $date = date('Y-m-d H:i:s');
                $editpost->editDB($_POST["content"], $_POST["title"], $date);
                if(!isset($_POST["addNext"]) || $_POST["addNext"]!= 1)
                {
                    header("Location: post.php?id=".$editpost->getAttribute("id"));
                }
                else
                {
                    session_start();
                    $_SESSION['message'] = 'Pomyślnie dodano posta!';
                    header("Location:".preg_replace('/\?.*/', '',$_SERVER['HTTP_REFERER']));
                }
                die();
            }
            else
            {
                $db = Database::connectToDB();
                $cat_id = $_POST["selectCategory"];
                $post = new Post($_POST);
                $post->save($db)->assignCategory($cat_id, $db);
                if(!isset($_POST["addNext"]) || $_POST["addNext"]!= 1)
                {
                    header("Location: post.php?id=".$post->getAttribute("id"));
                }
                else
                {
                    session_start();
                    $_SESSION['message'] = 'Pomyślnie dodano posta!';
                    header("Location:".$_SERVER['HTTP_REFERER']);
                }
                die();
            }
        }
    ?>
<head>
    <link rel="stylesheet"
    type="text/css"
    href="style.css"/>

</head>

<body>
    <form method="post" action="" class="postForm">
        <?php if($id = $editpost->getAttribute("id"))
        {
            ?>
                <input type="hidden" name="id" value="<?php echo $id;?>"/>
            <?php
        } 
        ?>
        <a href="blog.php">Przejdź do bloga</a>
        <label><input type="text" name="title" class="postTitle" placeholder="Tytuł posta..." value="<?php echo $editpost->getAttribute("title");?>"/></label>
        <label><textarea name="content" class="postContent" placeholder="Treśc posta..."><?php  echo $editpost->getAttribute("content");?></textarea></label>
        <label>
            <select name="selectCategory" class="selectCategory">
                <?php
                    $db = Database::connectToDB();
                    $categories = Category::getCategories($db);
                    foreach($categories as $category)
                    {
                        ?>
                            <option value="<?php echo $category["id"]; ?>" <?php echo $category["id"] == $post2category["id"] ? "selected" : '' ?>><?php echo $category["name"]; ?></option>
                        <?php
                    }
                ?>
            </select>
        </label>
        <label><input type="checkbox" name="addNext" class="addNext"value="1">Zapisz i dodaj nowy</input></label>
        <label><input type="submit" name="addPost" class="addPost" value="Dodaj"/></label>
    </form>
</body>