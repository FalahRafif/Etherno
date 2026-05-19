<div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Switcher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="border-bottom border-block-end-dashed">
            <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                <button class="nav-link active" id="switcher-home-tab" data-bs-toggle="tab" data-bs-target="#switcher-home"
                    type="button" role="tab" aria-controls="switcher-home" aria-selected="true">Theme Styles</button>
                <button class="nav-link" id="switcher-profile-tab" data-bs-toggle="tab" data-bs-target="#switcher-profile"
                    type="button" role="tab" aria-controls="switcher-profile" aria-selected="false">Theme Colors</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel" aria-labelledby="switcher-home-tab" tabindex="0">
                <p class="switcher-style-head">Theme Color Mode:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade border-0" id="switcher-profile" role="tabpanel" aria-labelledby="switcher-profile-tab" tabindex="0">
                <div class="theme-colors">
                    <p class="switcher-style-head">Menu Colors:</p>
                    <div class="d-flex switcher-style pb-2">
                        <div class="form-check switch-select me-3"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-grid canvas-footer">
            <a href="javascript:void(0);" id="reset-all" class="btn btn-danger btn-block m-1">Reset</a>
        </div>
    </div>
</div>
