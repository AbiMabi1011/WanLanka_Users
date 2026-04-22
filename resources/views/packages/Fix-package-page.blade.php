@extends('layouts.master')

@section('title', 'Fixed Packages | WanLanka')

@section('content')
<div class="main-container bg-gradient-animate py-5">
    <div class="container" data-aos="fade-up">
        <div class="page-header text-center mb-5">
            <h1 class="page-title text-neon">Fixed Packages</h1>
            <p class="page-subtitle text-white-50">Discover our exclusive collection of curated experiences</p>
        </div>

        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar-col glass p-4 rounded-4" data-aos="fade-right">
                    @include('include.package-filter-sidebar')
                </div>
            </div>

            <!-- Package List -->
            <div class="col-lg-9">
                <div id="package-list" class="row g-4">
                    @foreach ($packages as $index => $package)
                        <div class="col-md-6 col-xl-4" data-aos="zoom-in" data-aos-delay="{{ ($index % 3) * 100 }}">
                            <div class="package-card card-3d glass h-100">
                                <div class="package-image-wrapper img-3d-container">
                                    @if ($package['cover_image'])
                                        <img src="{{ $package['cover_image'] }}" alt="{{ $package['package_name'] }}" class="img-fluid" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                                    @else
                                        <img src="{{ asset('images/placeholder.jpg') }}" alt="No Image" class="img-fluid">
                                    @endif
                                    <div class="package-badge badge bg-accent">HOT</div>
                                </div>
                                <div class="package-content p-4">
                                    <h3 class="package-title text-white h5 mb-3">{{ $package['package_name'] }}</h3>
                                    <p class="package-description text-white-50 small mb-4">
                                        {{ Str::limit($package['description'] ?? 'No description available', 100) }}
                                    </p>
                                    <div class="package-footer d-flex justify-content-between align-items-center mt-auto border-top border-white-10 pt-3">
                                        <div class="package-price text-neon fw-bold h4 mb-0">RS{{ $package['price'] ?? 'N/A' }}</div>
                                        <a href="/packages/{{ $package['id'] }}" class="btn btn-premium btn-sm">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (empty($packages))
                        <div class="col-12 text-center py-5">
                            <div class="no-packages-icon h1 opacity-25">📦</div>
                            <p class="text-white-50">No packages available at the moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .package-card { transition: all 0.4s ease; overflow: hidden; display: flex; flex-direction: column; }
    .package-image-wrapper { height: 200px; overflow: hidden; position: relative; }
    .package-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .package-badge { position: absolute; top: 15px; right: 15px; z-index: 2; padding: 8px 15px; border-radius: 20px; }
    .bg-accent { background: #635BFF; }
    .border-white-10 { border-color: rgba(255,255,255,0.1) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Migration of filter logic
        const form = document.getElementById('package-filter-form');
        const packageList = document.getElementById('package-list');

        if(form && packageList) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                fetchPackages();
            });

            const resetButton = document.getElementById('reset-filters');
            if(resetButton) {
                resetButton.addEventListener('click', function () {
                    form.reset();
                    fetchPackages();
                });
            }
        }

        async function fetchPackages() {
            const formData = new FormData(form);
            const params = new URLSearchParams();
            formData.forEach((value, key) => { if (value) params.append(key, value); });

            packageList.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-white mt-3">Loading packages...</p></div>';

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/packages?${params.toString()}`, {
                    headers: {
                        'Authorization': 'Bearer 1|8OImmqdUzzCwAOzoksoHFeOjpz1iSWSLTbTL3geC43aa48db',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                packageList.innerHTML = '';
                
                if (data.length === 0) {
                    packageList.innerHTML = '<div class="col-12 text-center py-5 text-white-50">No packages found with these filters.</div>';
                    return;
                }

                data.forEach((pkg, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-xl-4';
                    col.setAttribute('data-aos', 'zoom-in');
                    col.setAttribute('data-aos-delay', (index % 3) * 100);
                    
                    col.innerHTML = `
                        <div class="package-card card-3d glass h-100">
                            <div class="package-image-wrapper img-3d-container">
                                <img src="${pkg.cover_image || '{{ asset('images/placeholder.jpg') }}'}" class="img-fluid">
                                <div class="package-badge badge bg-accent">FEATURED</div>
                            </div>
                            <div class="package-content p-4">
                                <h3 class="package-title text-white h5 mb-3">${pkg.package_name}</h3>
                                <p class="package-description text-white-50 small mb-4">${pkg.description || ''}</p>
                                <div class="package-footer d-flex justify-content-between align-items-center mt-auto border-top border-white-10 pt-3">
                                    <div class="package-price text-neon fw-bold h4 mb-0">RS${pkg.price || 'N/A'}</div>
                                    <a href="/packages/${pkg.id}" class="btn btn-premium btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    `;
                    packageList.appendChild(col);
                });
                
                // Re-init AOS for new elements
                if(typeof AOS !== 'undefined') AOS.refresh();

            } catch (error) {
                console.error('Error:', error);
                packageList.innerHTML = '<div class="col-12 text-center py-5 text-danger">Error loading packages.</div>';
            }
        }
    });
</script>
@endsection
