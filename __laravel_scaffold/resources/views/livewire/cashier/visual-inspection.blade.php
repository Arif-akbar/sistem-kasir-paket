<section class="md-card" x-data="visualInspection()" x-init="boot()">
    <div class="md-card-header flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-950">Visual Check</h2>
        @if ($result)
            <button type="button" wire:click="clearResult" class="text-sm font-medium text-slate-600 hover:text-slate-950">
                Clear
            </button>
        @endif
    </div>

    <div class="space-y-4 p-5">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-950">
            <video x-ref="video" class="aspect-video w-full object-cover" autoplay muted playsinline></video>
            <canvas x-ref="canvas" class="hidden"></canvas>
        </div>

        <button type="button" class="md-btn-secondary w-full" x-on:click="capture">
            <span class="material-symbols-outlined">photo_camera</span>
            Inspect Frame
        </button>

        @error('frame') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        @if ($result)
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-medium text-slate-600">Classification</p>
                <p class="mt-1 text-lg font-semibold text-slate-950">{{ Str::headline((string) ($result['label'] ?? 'manual_review')) }}</p>
                <p class="text-sm text-slate-600">{{ number_format((float) ($result['confidence'] ?? 0), 2) }} confidence</p>
            </div>
        @endif
    </div>

    <script>
        function visualInspection() {
            return {
                async boot() {
                    if (!navigator.mediaDevices?.getUserMedia) {
                        return;
                    }

                    try {
                        this.$refs.video.srcObject = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment' },
                            audio: false,
                        });
                    } catch (error) {
                        console.warn('Camera unavailable', error);
                    }
                },
                capture() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;

                    if (!video.videoWidth) {
                        return;
                    }

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    this.$wire.set('frame', canvas.toDataURL('image/jpeg', 0.82));
                    this.$wire.inspect();
                },
            };
        }
    </script>
</section>
