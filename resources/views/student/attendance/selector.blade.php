<div class="mt-4 mb-4">
    <x-input.input-label for="status" :value="__('Status')" />
    <x-input.select-input id="status" class="mt-1 w-full" type="text" name="status" required autofocus
        autocomplete="status">
        <option value="" disabled selected>Pilih Status Kehadiran</option>
        <option value="Hadir">Hadir</option>
        <option value="Absen Mapel">Absen Mapel</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
    </x-input.select-input>
</div>
<div class="hidden" id="permit">
    <x-input.input-label for="izin" :value="__('Keterangan')" />
    <x-input.text-input id="izin" name="permit" class="mt-1 w-full" type="text" maxlength="50"
        placeholder="Acara Keluarga" required />
</div>
<div class="hidden" id="fileUpload">
    <x-input.input-label for="sickFile" :value="__('Surat Keterangan')" />
    <x-input.text-input id="sickFile" name="sickFile" class="mt-1 w-full" type="file" maxlength="50" required />
</div>
