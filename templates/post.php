<a href="/post/viewPost.html" class="text-decoration-none">
    <div class="card bg-secondary text-light mb-3 p-3 zoom">
        <div class="card-body text-center">
            <div class="d-flex justify-content-between">
                <!-- Tags -->
                <span class="text-start h6 text-body-emphasis opacity-75 rounded">
                    <?php foreach(explode(',', $row['Tags']) as $tag): ?>
                        <span class="badge bg-light text-dark m-1 rounded-pill"><?php echo $tag; ?></span>
                    <?php endforeach; ?>
                </span>
                <!-- Time -->
                <span class="badge"><?php echo $row['PostDateTime']; ?> mins ago</span>
            </div>
            <!-- post title  -->
            <h3 class="card-title mt-lg-4 mb-4 text-light"><?php echo $row['PostTitle']; ?></h3>
            <!-- post image -->
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['PostImage']); ?>" alt="Post Image" class="card-image img-fluid mb-3">
            <!-- slider rating -->
            <a>
                <div class="text-start mt-3">
                    <!-- Read only slider, disabled just for aggregate rating, actually rate on view page -->
                    <div class="d-flex justify-content-end">
                        <input type="range" class="form-range" id="rating" value="<?php echo $row['Rating']; ?>" min="0" max="5.5" step="0.01" disabled>
                        <p class="mx-2" for="rating"><span id="aggregate-rate"><?php echo $row['Rating']; ?></span>/5.5</p>
                    </div>
                </div>
            </a>
            <!-- End slider rating -->
        </div>
    </div>
</a>