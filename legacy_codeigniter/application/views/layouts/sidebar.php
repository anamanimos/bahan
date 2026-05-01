				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					<!--begin::Sidebar-->
					<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
						<!--begin::Logo-->
						<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
							<!--begin::Logo image-->
							<a href="<?= base_url('dashboard') ?>">
								<img alt="Logo" src="<?= base_url('assets/vendors/media/logos/default-dark.svg') ?>" class="h-25px app-sidebar-logo-default" />
								<img alt="Logo" src="<?= base_url('assets/vendors/media/logos/default-small.svg') ?>" class="h-20px app-sidebar-logo-minimize" />
							</a>
							<!--end::Logo image-->
							<!--begin::Sidebar toggle-->
							<div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
								<i class="ki-duotone ki-double-left fs-2 rotate-180">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</div>
							<!--end::Sidebar toggle-->
						</div>
						<!--end::Logo-->
						<!--begin::sidebar menu-->
						<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
							<!--begin::Menu wrapper-->
							<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
								<!--begin::Menu-->
								<div class="menu menu-column menu-rounded menu-sub-indention px-3" id="kt_app_sidebar_menu" data-kt-menu="true">
									<!--begin:Menu item-->
									<div class="menu-item">
										<!--begin:Menu link-->
										<a class="menu-link <?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
											<span class="menu-icon">
												<i class="ki-duotone ki-element-11 fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">Dashboard</span>
										</a>
										<!--end:Menu link-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div class="menu-item pt-5">
										<!--begin:Menu content-->
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Inventory</span>
										</div>
										<!--end:Menu content-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link <?= (isset($active_menu) && $active_menu == 'pr') ? 'active' : '' ?>" href="<?= base_url('inventory/purchase-requisition') ?>">
											<span class="menu-icon">
												<i class="ki-duotone ki-notepad fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
												</i>
											</span>
											<span class="menu-title">Pengajuan Beli (PR)</span>
										</a>
									</div>
									<div class="menu-item">
										<a class="menu-link <?= (isset($active_menu) && $active_menu == 'gr') ? 'active' : '' ?>" href="<?= base_url('inventory/goods-receipt') ?>">
											<span class="menu-icon">
												<i class="ki-duotone ki-delivery-2 fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
													<span class="path6"></span>
													<span class="path7"></span>
													<span class="path8"></span>
													<span class="path9"></span>
												</i>
											</span>
											<span class="menu-title">Input Nota (GR)</span>
										</a>
									</div>
									<div class="menu-item">
										<a class="menu-link <?= (isset($active_menu) && $active_menu == 'stocks') ? 'active' : '' ?>" href="<?= base_url('inventory/stocks') ?>">
											<span class="menu-icon">
												<i class="ki-duotone ki-chart-line-star fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
												</i>
											</span>
											<span class="menu-title">Stok</span>
										</a>
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?= (isset($active_group) && $active_group == 'sales') ? 'here show' : '' ?>">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-handcart fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
                                                    <span class="path5"></span>
												</i>
											</span>
											<span class="menu-title">Sales & POS</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link <?= (isset($active_menu) && $active_menu == 'pos') ? 'active' : '' ?>" href="<?= base_url('sales/pos') ?>">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Point of Sale (POS)</span>
												</a>
											</div>
											<div class="menu-item">
												<a class="menu-link <?= (isset($active_menu) && $active_menu == 'requests') ? 'active' : '' ?>" href="<?= base_url('sales/requests') ?>">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Internal Request</span>
												</a>
											</div>
										</div>
									</div>
									<!--begin:Menu item-->
									<div class="menu-item pt-5">
										<!--begin:Menu content-->
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
										</div>
										<!--end:Menu content-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link <?= (isset($active_menu) && $active_menu == 'products') ? 'active' : '' ?>" href="<?= base_url('master/products') ?>">
											<span class="menu-icon">
												<i class="ki-duotone ki-basket fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">Produk List</span>
										</a>
									</div>
									<!--end:Menu item-->
								</div>
								<!--end::Menu-->
							</div>
							<!--end::Menu wrapper-->
						</div>
						<!--end::sidebar menu-->
					</div>
					<!--end::Sidebar-->
