<?php
// Search Bar Component
?>
<div class="home-search reveal">
    <form action="search.php" method="get" class="search-form">
        <div class="search-field">
            <label for="searchKeyword">Search</label>
            <input type="text" id="searchKeyword" name="q" placeholder="Search courses, mentors, research..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        </div>
        <div class="search-field">
            <label for="searchCategory">Category</label>
            <select id="searchCategory" name="category">
                <option value="">All Categories</option>
                <option value="engineering">Engineering</option>
                <option value="medicine">Medical</option>
                <option value="business">Business</option>
                <option value="computer-science">Computer Science</option>
                <option value="arts">Arts &amp; Design</option>
                <option value="law">Law</option>
            </select>
        </div>
        <div class="search-field">
            <label for="searchType">Type</label>
            <select id="searchType" name="type">
                <option value="">Everything</option>
                <option value="course">Courses</option>
                <option value="mentor">Mentors</option>
                <option value="research">Research</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
    </form>
</div>
