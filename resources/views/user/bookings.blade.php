@extends('layouts.master')

@section('title', 'My Bookings | WanLanka')

@section('content')
<main class="bg-gradient-animate py-5">
    <div class="container" data-aos="fade-up">
        <!-- 🌟 Main Heading -->
        <div class="text-center mb-5">
            <h2 class="text-neon mb-3">My Bookings & Packages</h2>
            <div class="mx-auto" style="width: 80px; height: 4px; background: #635BFF; border-radius: 2px;"></div>
        </div>

        <!-- 🌟 Tabs Navigation -->
        <div class="glass rounded-pill p-2 mb-5 mx-auto max-w-900" data-aos="fade-down">
            <ul class="nav nav-pills nav-justified" id="bookingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active text-white" id="currentBooking-tab" data-bs-toggle="tab" href="#currentBooking">🚗 Current</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="pastBooking-tab" data-bs-toggle="tab" href="#pastBooking">📅 Past</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="currentPackage-tab" data-bs-toggle="tab" href="#currentPackage">💼 Packages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="currentFixed-tab" data-bs-toggle="tab" href="#currentFixed">🎟️ Fixed</a>
                </li>
            </ul>
        </div>

        <!-- 🌟 Tab Content -->
        <div class="tab-content mt-4" data-aos="fade-up">
            {{-- 🚗 Current Bookings --}}
            <div class="tab-pane fade show active" id="currentBooking">
                <div class="row g-4">
                    @forelse($currentBookings as $booking)
                        <div class="col-md-6 col-lg-4">
                            <div class="card-3d glass p-4 h-100 rounded-4 border-accent">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-white h6 mb-0">{{ $booking->pickup_district }}</h5>
                                    <span class="badge bg-warning text-dark">{{ ucfirst($booking->status) }}</span>
                                </div>
                                <div class="text-white-50 small mb-3">
                                    <div class="mb-1"><i class="far fa-calendar-alt me-2"></i> {{ $booking->date }}</div>
                                    <div><i class="far fa-clock me-2"></i> {{ $booking->time }}</div>
                                </div>
                                <div class="d-flex gap-2 mt-auto pt-3 border-top border-white-10">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-premium btn-sm flex-grow-1">View</a>
                                    @if(in_array(strtolower($booking->status), ['cancelled', 'completed']))
                                        <form action="{{ route('userbookings.forceDelete', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete permanently?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-white-50 italic">No current bookings available.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 📅 Past Bookings --}}
            <div class="tab-pane fade" id="pastBooking">
                <div class="row g-4">
                    @forelse($pastBookings as $booking)
                        <div class="col-md-6 col-lg-4">
                            <div class="card-3d glass p-4 h-100 rounded-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-white h6 mb-0">{{ $booking->pickup_district }}</h5>
                                    <span class="badge bg-success">{{ ucfirst($booking->status) }}</span>
                                </div>
                                <div class="text-white-50 small mb-3">
                                    <div class="mb-1"><i class="far fa-calendar-alt me-2"></i> {{ $booking->date }}</div>
                                    <div><i class="far fa-clock me-2"></i> {{ $booking->time }}</div>
                                </div>
                                <div class="d-flex gap-2 mt-auto pt-3 border-top border-white-10">
                                    <form action="{{ route('userbookings.rebook', $booking->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-premium btn-sm w-100">Rebook</button>
                                    </form>
                                    @if(in_array(strtolower($booking->status), ['cancelled', 'completed']))
                                        <form action="{{ route('userbookings.forceDelete', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete permanently?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-white-50 italic">No past bookings found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 💼 Current Packages --}}
            <div class="tab-pane fade" id="currentPackage">
                <div class="row g-4">
                    @forelse($currentPackages as $package)
                        <div class="col-md-6">
                            <div class="card-3d glass p-4 h-100 rounded-4 border-accent">
                                <h5 class="text-neon h6 mb-3">{{ $package->title }}</h5>
                                <p class="text-white-50 small mb-4">{{ $package->description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-white-10">
                                    <div class="text-white fw-bold">LKR {{ number_format($package->price, 2) }}</div>
                                    <button class="btn btn-premium btn-sm">View Details</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-white-50 italic">No active packages available.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 🎟️ Current Fixed Bookings --}}
            <div class="tab-pane fade" id="currentFixed">
                @if(isset($currentFixedBookings) && $currentFixedBookings->count() > 0)
                    <div class="table-responsive glass p-4 rounded-4 border-accent">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="text-white-50 small">
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                    <th>Total</th>
                                    <th>Receipt</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-white small">
                                @foreach($currentFixedBookings as $booking)
                                <tr>
                                    <td>{{ $booking->package_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($booking->status) }}</span></td>
                                    <td>{{ $booking->participants }}</td>
                                    <td>Rs {{ number_format($booking->total_price, 2) }}</td>
                                    <td>
                                        @if($booking->receipt)
                                            <a href="{{ Storage::url($booking->receipt) }}" target="_blank" class="text-neon">View</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white-50 text-center py-5 italic">No current fixed bookings available.</p>
                @endif
            </div>
        </div>
    </div>
</main>

<style>
    .nav-pills .nav-link.active { background: #635BFF !important; box-shadow: 0 0 15px rgba(99, 91, 255, 0.4); }
    .max-w-900 { max-width: 700px; }
    .border-accent { border: 1px solid rgba(99, 91, 255, 0.2) !important; }
    .border-white-10 { border-color: rgba(255,255,255,0.1) !important; }
    .table-dark { background: transparent !important; }
    .table-dark thead th { border-bottom: 1px solid rgba(255,255,255,0.1); }
</style>
@endsection
