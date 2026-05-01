					<!--begin::Footer-->
					<div id="kt_app_footer" class="app-footer">
						<!--begin::Footer container-->
						<div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
							<!--begin::Copyright-->
							<div class="text-gray-900 order-2 order-md-1">
								<span class="text-muted fw-semibold me-1">2024&copy;</span>
								<a href="#" target="_blank" class="text-gray-800 text-hover-primary">Toko Kain Garment</a>
							</div>
							<!--end::Copyright-->
							<!--begin::Menu-->
							<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
								<li class="menu-item">
									<a href="#" target="_blank" class="menu-link px-2">Support</a>
								</li>
							</ul>
							<!--end::Menu-->
						</div>
						<!--end::Footer container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::App-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-duotone ki-arrow-up">
				<span class="path1"></span>
				<span class="path2"></span>
			</i>
		</div>
		<!--end::Scrolltop-->
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="<?= base_url('assets/vendors/plugins/global/plugins.bundle.js') ?>"></script>
		<script src="<?= base_url('assets/vendors/js/scripts.bundle.js') ?>"></script>
		<!--end::Global Javascript Bundle-->
        <!--begin::Custom Javascript-->
        <?php if (isset($js)): ?>
            <?php foreach ($js as $file): ?>
                <script src="<?= base_url($file) ?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>
        <!--end::Custom Javascript-->
	</body>
	<!--end::Body-->
</html>
