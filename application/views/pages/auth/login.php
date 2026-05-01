<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<title>Login - Toko Kain Integrated System</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="shortcut icon" href="<?= base_url('assets/vendors/media/logos/favicon.ico') ?>" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="<?= base_url('assets/vendors/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" type="text/css" />
		<link href="<?= base_url('assets/vendors/css/style.bundle.css') ?>" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Page bg image-->
			<style>body { background-image: url('<?= base_url('assets/vendors/media/auth/bg4.jpg') ?>'); } [data-bs-theme="dark"] body { background-image: url('<?= base_url('assets/vendors/media/auth/bg4-dark.jpg') ?>'); }</style>
			<!--end::Page bg image-->
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid flex-lg-row">
				<!--begin::Aside-->
				<div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
					<!--begin::Aside-->
					<div class="d-flex flex-center flex-lg-start flex-column">
						<!--begin::Logo-->
						<a href="<?= base_url() ?>" class="mb-7">
							<img alt="Logo" src="<?= base_url('assets/vendors/media/logos/custom-3.svg') ?>" />
						</a>
						<!--end::Logo-->
						<!--begin::Title-->
						<h2 class="text-white fw-normal m-0">Integrated Inventory & POS System</h2>
						<!--end::Title-->
					</div>
					<!--begin::Aside-->
				</div>
				<!--end::Aside-->
				<!--begin::Body-->
				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
					<!--begin::Card-->
					<div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
						<!--begin::Wrapper-->
						<div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
							<!--begin::Form-->
							<div class="form w-100 text-center">
								<!--begin::Heading-->
								<div class="text-center mb-11">
									<!--begin::Title-->
									<h1 class="text-gray-900 fw-bolder mb-3">Selamat Datang</h1>
									<!--end::Title-->
									<!--begin::Subtitle-->
									<div class="text-gray-500 fw-semibold fs-6">Aplikasi Toko Kain & Bahan Pelengkap</div>
									<!--end::Subtitle-->
								</div>
								<!--begin::Heading-->
                                <?php if ($this->session->flashdata('error')): ?>
                                    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                                <?php endif; ?>
								<!--begin::Submit button-->
								<div class="d-grid mb-10">
									<a href="<?= base_url('auth/login/verify_sso?token=demo&email=admin@demo.com') ?>" class="btn btn-primary">
										<!--begin::Indicator label-->
										<span class="indicator-label">Login via ERP SSO</span>
										<!--end::Indicator label-->
									</a>
								</div>
								<!--end::Submit button-->
                                <div class="text-gray-500 text-center fw-semibold fs-6">Autentikasi dikelola secara terpusat melalui sistem ERP.</div>
							</div>
							<!--end::Form-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Card-->
				</div>
				<!--end::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="<?= base_url('assets/vendors/plugins/global/plugins.bundle.js') ?>"></script>
		<script src="<?= base_url('assets/vendors/js/scripts.bundle.js') ?>"></script>
		<!--end::Global Javascript Bundle-->
	</body>
	<!--end::Body-->
</html>
