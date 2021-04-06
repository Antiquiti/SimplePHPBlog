<?php
    require("class_library.php");
    $post = Post::getPost($_GET["id"]);
    $cat_id = Post::getCategory($_GET["id"]);
    $category = Category::getCategory($cat_id["category_id"]);
?>
<head>
    <link rel="stylesheet"
    type="text/css"
    href="style.css"/>
</head>

<body>
    <article>
        <h1 style="text-align: center"><?php echo $post->getAttribute("title");?></h1>
        <p class="singlePostDate">Data dodania: <?php echo $post->getAttribute("creation_date");?></p>
        <p class="singlePostDate">Data edycji: <?php echo $post->getAttribute("update_date");?></p>
        <?php if(isset($_POST["editPost"]))
        {
            ?>
            <div style="text-align: center"><textarea class="postContent" ><?php echo $post->getAttribute("content");?></textarea></div>
            <?php
        }
        else
        {
        ?>
            <div class="divPostContent"><p class="singlePostContent"><?php echo $post->getAttribute("content");?></p></div>
            <?php
        }
        ?>
        <a class ="editPost" href="newPost.php?editPost=<?php echo $post->getAttribute("id");?>">Edytuj posta</a>
        <p class="singlePostDate"><label style="color: blue">Kategoria:</label> <?php echo $category["name"];?></p>
    </article>
    <div class="ahrefBlog">
        <a href="blog.php">Przejdź do bloga</a>
    </div>

</body>