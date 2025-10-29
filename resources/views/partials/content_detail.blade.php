@if ($p->gambar || $p->detil)
    <div>
        @if ($p->gambar)
            <img src="{{ asset('storage/' . $p->gambar) }}" 
                 class="img-fluid rounded shadow-sm"
                 style="cursor: pointer; max-width: 150px;"
                 data-bs-toggle="modal" data-bs-target="#gambarModal{{ $p->id }}">
        @endif
        @if ($p->detil)
            <p class="mt-2">{{ $p->detil }}</p>
        @endif
    </div>

    @if ($p->gambar)
        <div class="modal fade" id="gambarModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">Gambar detil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $p->gambar) }}" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <em> - </em>
@endif
