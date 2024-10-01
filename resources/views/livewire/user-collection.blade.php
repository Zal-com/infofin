<div>
    {{$this->table}}
</div>
@script
<script>
    Livewire.on('copy-link', link => {
        navigator.clipboard.writeText(link[0].link);
    })
</script>
@endscript
