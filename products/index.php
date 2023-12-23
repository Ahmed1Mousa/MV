<?php 
$category_ids = isset($_GET['cids']) ? $_GET['cids'] : 'all';
?>
<div class="content py-3">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline rounded-0 card-primary shadow">
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input custom-control-input-primary custom-control-input-outline cat_all" type="checkbox" id="cat_all" <?= !is_array($category_ids) && $category_ids =='all' ? "checked" : "" ?>>
                                <label for="cat_all" class="custom-control-label"> All</label>
                            </div>
                        </div>
                        <?php 
                        $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and status = 1 order by `name` asc ");
                        while($row = $categories->fetch_assoc()):
                        ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input custom-control-input-primary custom-control-input-outline cat_item" type="checkbox" id="cat_item<?= $row['id'] ?>" <?= in_array($row['id'],explode(',',$category_ids)) ? "checked" : '' ?> value="<?= $row['id'] ?>">
                                <label for="cat_item<?= $row['id'] ?>" class="custom-control-label"> <?= $row['name'] ?></label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow rounded-0">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row justify-content-center mb-3">
                            <div class="col-lg-8 col-md-10 col-sm-12">
                                <form action="" id="search-frm">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Search</span></div>
                                        <input type="search" id="search" class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row" id="product_list">
                            <?php 
                            $swhere = "";
                            if(!empty($category_ids)):
                            if($category_ids !='all'){
                                $swhere = " and p.category_id in ({$category_ids}) ";
                            }
                            if(isset($_GET['search']) && !empty($_GET['search'])){
                                $swhere .= " and (p.name LIKE '%{$_GET['search']}%' or p.description LIKE '%{$_GET['search']}%' or c.name LIKE '%{$_GET['search']}%' or v.shop_name LIKE '%{$_GET['search']}%') ";
                            }

                            $products = $conn->query("SELECT p.*, v.shop_name as vendor, c.name as `category`, p.is_100_percent_egyptian FROM `product_list` p INNER JOIN vendor_list v ON p.vendor_id = v.id INNER JOIN category_list c ON p.category_id = c.id WHERE p.delete_flag = 0 AND p.`status` = 1 {$swhere} ORDER BY RAND()");
                            while ($row = $products->fetch_assoc()):
                                $expiry_date = strtotime($row['expiry_date']);
                                $days_until_expiry = ceil(($expiry_date - time()) / (60 * 60 * 24));

                                // Display discount for near-expiry products
                                $discount_html = '';
                                if ($days_until_expiry <= 7) {
                                    $discount = rand(5, 20); // Random discount percentage
                                    $discount_html = "<span class='text-danger'> {$days_until_expiry} days until expiry! Discount: {$discount}%</span>";

                                    // Check if the discount is not already set
                                    if ($row['discount'] == 0) {
                                        // Update the product's discount in the database
                                        $conn->query("UPDATE product_list SET discount = $discount WHERE id = {$row['id']}");
                                    }
                                }
                                // Include this line to pass the discount HTML as a parameter in the URL
                                $discount_param = urlencode($discount_html);
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-12 product-item">
                                <a href="./?page=products/view_product&id=<?= $row['id'] ?>&discount=<?= $discount_param ?>" class="card shadow rounded-0 text-reset text-decoration-none">
                                <div class="product-img-holder position-relative">
                                    <img src="<?= validate_image($row['image_path']) ?>" alt="Product-image" class="img-top product-img bg-gradient-gray">
                                </div>
                                    <div class="card-body border-top border-gray">
                                        <h5 class="card-title text-truncate w-100"><?= $row['name'] ?></h5>
                                        <div class="d-flex w-100">
                                            <div class="col-auto px-0"><small class="text-muted">Vendor: </small></div>
                                            <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="text-truncate m-0"><small class="text-muted"><?= $row['vendor'] ?></small></p></div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="col-auto px-0"><small class="text-muted">Category: </small></div>
                                            <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="text-truncate m-0"><small class="text-muted"><?= $row['category'] ?></small></p></div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <p class="m-0">
                                                <small class="text-muted">Price: <?= format_num($row['price']) ?></small>
                                            </p>
                                            <?php if (!empty($expiry_date)) : ?>
                                                <p class="m-0">
                                                    <small class="text-muted">Expiry Date: <?= date('Y-m-d', $expiry_date) ?></small>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($discount_html)) : ?>
                                                <p class="m-0">
                                                    <small class="text-danger">Discount: <?= $discount_html ?></small>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($row['is_100_percent_egyptian'] == 1) : ?>
                                            <span class="badge badge-egyptian">100% Egyptian</span>
                                        <?php endif; ?>
                                        <p class="card-text truncate-3 w-100"><?= strip_tags(html_entity_decode($row['description'])) ?></p>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12 text-center">
                                    Pleas select atleast 1 product category
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        if($('#cat_all').is(':checked') == true){
            $('.cat_item').prop('checked',true)
        }
        if($('.cat_item:checked').length == $('.cat_item').length){
            $('#cat_all').prop('checked',true)
        }
        $('.cat_item').change(function(){
            var ids = [];
            $('.cat_item:checked').each(function(){
                ids.push($(this).val())
            })
            location.href="./?page=products&cids="+(ids.join(","))
        })
        $('#cat_all').change(function(){
            if($(this).is(':checked') == true){
                $('.cat_item').prop('checked',true)
            }else{
                $('.cat_item').prop('checked',false)
            }
            $('.cat_item').trigger('change')
        })
        $('#search-frm').submit(function(e){
            e.preventDefault()
            var q = "search="+$('#search').val()
            if('<?= !empty($category_ids) && $category_ids !='all' ?>' == 1){
                q += "&cids=<?= $category_ids ?>"
            }
            location.href="./?page=products&"+q;

        })
    })
</script>