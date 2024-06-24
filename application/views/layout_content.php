<div class="card w-100" <?php echo isset($cardStyle) ? $cardStyle : '' ?> style="min-height: 100vh;">
    <?php if (!isset($showHeader) || $showHeader == true) : ?>
        <div class="card-header border-transparent">
            <div class="card-tools">
                <!-- Maximize Button -->
                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fa fa-expand"></i></button>
            </div>

            <?php if (isset($title) && isset($showTitle) && $showTitle) : ?>
                <div class="content-header">
                    <h1 class="font-weight-bold"><?php echo strtoupper($title) ?></h1>
                </div>
            <?php endif ?>

            <div class="d-flex flex-row-reverse">
                <a class="float-right btn btn-outline-primary chip realtime-clock text-xs mb-0"><?php echo getLocaleTime(strftime("%A, %d %B %Y %H:%M:%S", time())) ?></a>
            </div>
        </div>
    <?php endif ?>

    <div class="card-body" style="padding-top: 0;">
        <?php $this->load->view($view) ?>
    </div>
</div>