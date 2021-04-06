<head>
    <link rel="stylesheet"
    type="text/css"
    href="style.css"/>

</head>

<body>
    <?php
        require("class_library.php");
    ?>
    
    <form method="post" action="" class="categoryForm">
        <input type="text" name="categoryName" class="categoryName" placeholder="Nazwa kategorii..."/>
        <input type="submit" name="addCategory" class="addCategory" value="Dodaj"/>
    </form>

    <div class="ahrefBlog">
        <a href="blog.php">Przejdź do bloga</a>
    </div>

    <?php
        
        if(isset($_POST["addCategory"]))
        {
            $categoryExist = false;
            $db = Database::connectToDB();
            $input = $_POST["categoryName"];
            $categories = Category::getCategories($db);
            foreach($categories as $category)
            {
                if($input == $category["name"])
                {
                    echo "<script type='text/javascript'>alert('Kategoria o podanej nazwie już istnieje');</script>";
                    $categoryExist = true;
                }
            }
            if($categoryExist == false)
            {
                $cat = new Category($input);
                $cat->save($db);
                echo "<script type='text/javascript'>alert('Kategoria pomyślnie dodana!');</script>";
            }
        }
    ?>
</body>

<?php

?>