<head>
    <link rel="stylesheet"
    type="text/css"
    href="style.css"/>

</head>

<body>
    <?php
        require("class_library.php");
    ?>
    
    <h1 align="center">BLOG</h1>
    <div class="ahrefBlog">
    <a href="newpost.php">Dodaj nowy post</a>
    <a href="newcategory.php">Dodaj nową kategorię</a>
    </div>

    <div class="grid_container">
        <?php
            $db = Database::connectToDB();
            $numberOfPages = Post::countPosts($db);
            $numberOfPages = ceil($numberOfPages/6);
            $pageNumber = 1;

            if(isset($_GET["page"]))
            {
                $pageNumber = $_GET["page"];
                $pageNumber == 1 ? $start = 0 : $start = ($pageNumber-1) * 6; 
                $posts = Post::getLimitedPosts($db, $start, 6);
            }
            else if(isset($_GET["cat"]))
            {
                $posts = Post::getPostsByCategory($db,$_GET["cat"]);
            }
            else
            {
                $posts = Post::getLimitedPosts($db, 0, 6);
            }

            foreach($posts as $post_object)
            {
                $cat_id = Post::getCategory($post_object->getAttribute("id"));
                $category = Category::getCategory($cat_id["category_id"])
            ?>
                <article class="blogArticle">
                    <h2 class="blogTitle"><?php echo $post_object->getAttribute("title");?></h2>
                    <p class="blogContent"><?php echo $post_object->getExcerpt();?></p>
                    <p class="showCategory">Kategoria: <a href="blog.php?cat=<?php echo $cat_id["category_id"];?>"><?php echo $category["name"];?></a></p>
                    <p class="readMore"><a href="post.php?id=<?php echo $post_object->getAttribute("id");?>" class="readMoreHREF">Czytaj więcej...</a></p>
                </article>

            <?php
            }
            ?>      
    </div>
    <div style="text-align: center">
        <?php
            for($i = 1; $i <= $numberOfPages; $i++)
            {
                ?>
                    <a style="color: <?php $i == $pageNumber ? $color ='chartreuse' : $color='blueviolet'; echo $color; ?>" href="blog.php?page=<?php echo $i?>"><?php echo $i?></a>
                <?php
            }
        ?>
    </div>
</body>