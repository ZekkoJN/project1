<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Mahasiswa (POST)</title>
    <link rel="stylesheet" href="style.css">
    <script>
        /**
         * Form Validator Class
         * Mengelola validasi form dengan standar yang baik
         */
        class FormValidator {
            constructor(formName) {
                this.form = document.forms[formName];
                this.fieldOrder = [
                    'nim', 'nama', 'tempat_lahir', 'tanggal_lahir', 
                    'alamat', 'kota', 'jk', 'email', 'no_hp', 
                    'umur', 'status', 'hobi[]'
                ];
                
                // Konfigurasi validasi untuk setiap field
                this.validationRules = {
                    nim: {
                        required: true,
                        pattern: /^[0-9]+$/,
                        errorMsg: 'NIM harus berisi angka saja'
                    },
                    nama: {
                        required: true,
                        pattern: /^[a-zA-Z\s]+$/,
                        errorMsg: 'Nama tidak boleh mengandung angka',
                        emptyMsg: 'Isian Nama tidak boleh kosong!'
                    },
                    tempat_lahir: {
                        required: true,
                        pattern: /^[a-zA-Z\s]+$/,
                        errorMsg: 'Tempat lahir tidak boleh mengandung angka'
                    },
                    tanggal_lahir: {
                        required: true,
                        custom: (value) => {
                            const date = new Date(value);
                            const today = new Date();
                            return date < today;
                        },
                        errorMsg: 'Tanggal lahir tidak valid'
                    },
                    alamat: {
                        required: true,
                        minLength: 10,
                        errorMsg: 'Alamat minimal 10 karakter'
                    },
                    kota: {
                        required: true,
                        errorMsg: 'Silakan pilih kota'
                    },
                    email: {
                        required: true,
                        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                        errorMsg: 'Format email tidak valid'
                    },
                    no_hp: {
                        required: true,
                        pattern: /^[0-9+\-\s()]+$/,
                        minLength: 10,
                        maxLength: 15,
                        errorMsg: 'Nomor HP tidak valid (10-15 digit)'
                    },
                    umur: {
                        required: true,
                        pattern: /^[0-9]+$/,
                        custom: (value) => {
                            const age = parseInt(value);
                            return age >= 17 && age <= 100;
                        },
                        errorMsg: 'Umur harus berisi angka (17-100 tahun)',
                        emptyMsg: 'Isian Umur tidak boleh kosong!'
                    }
                };
            }

            /**
             * Validasi field berdasarkan aturan yang ditentukan
             */
            validateField(fieldName, showAlert = true) {
                const field = this.form.elements[fieldName];
                if (!field) return false;

                const value = field.value.trim();
                const rules = this.validationRules[fieldName];

                if (!rules) {
                    // Jika tidak ada aturan khusus, cek hanya required
                    if (value === '') {
                        if (showAlert) {
                            alert(`Field ${fieldName} tidak boleh kosong`);
                            field.focus();
                        }
                        return false;
                    }
                    return true;
                }

                // Cek required
                if (rules.required && value === '') {
                    if (showAlert) {
                        alert(rules.emptyMsg || `Field ${fieldName} tidak boleh kosong`);
                        field.focus();
                    }
                    return false;
                }

                // Cek pattern
                if (rules.pattern && !rules.pattern.test(value)) {
                    if (showAlert) {
                        alert(rules.errorMsg);
                        field.value = '';
                        field.focus();
                    }
                    return false;
                }

                // Cek minLength
                if (rules.minLength && value.length < rules.minLength) {
                    if (showAlert) {
                        alert(rules.errorMsg || `Minimal ${rules.minLength} karakter`);
                        field.focus();
                    }
                    return false;
                }

                // Cek maxLength
                if (rules.maxLength && value.length > rules.maxLength) {
                    if (showAlert) {
                        alert(rules.errorMsg || `Maksimal ${rules.maxLength} karakter`);
                        field.focus();
                    }
                    return false;
                }

                // Cek custom validation
                if (rules.custom && !rules.custom(value)) {
                    if (showAlert) {
                        alert(rules.errorMsg);
                        field.focus();
                    }
                    return false;
                }

                return true;
            }

            /**
             * Validasi radio button (minimal 1 dipilih)
             */
            validateRadio(fieldName) {
                const radios = this.form.elements[fieldName];
                if (!radios) return false;

                for (let i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        return true;
                    }
                }
                return false;
            }

            /**
             * Validasi checkbox (minimal 1 dipilih)
             */
            validateCheckbox(fieldName = 'hobi[]') {
                const checkboxes = document.querySelectorAll(`input[name="${fieldName}"]:checked`);
                return checkboxes.length > 0;
            }

            /**
             * Enable field berikutnya setelah validasi berhasil
             */
            enableNextField(currentFieldName) {
                const currentIndex = this.fieldOrder.indexOf(currentFieldName);
                if (currentIndex === -1 || currentIndex >= this.fieldOrder.length - 1) {
                    return;
                }

                const nextFieldName = this.fieldOrder[currentIndex + 1];
                this.enableField(nextFieldName);
            }

            /**
             * Enable field tertentu
             */
            enableField(fieldName) {
                if (fieldName === 'jk' || fieldName === 'status') {
                    // Radio button
                    const radios = this.form.elements[fieldName];
                    if (radios) {
                        for (let i = 0; i < radios.length; i++) {
                            radios[i].disabled = false;
                            radios[i].parentElement.style.opacity = '1';
                            radios[i].parentElement.style.pointerEvents = 'auto';
                        }
                    }
                } else if (fieldName === 'hobi[]') {
                    // Checkbox
                    const checkboxes = document.querySelectorAll('input[name="hobi[]"]');
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                        cb.parentElement.style.opacity = '1';
                        cb.parentElement.style.pointerEvents = 'auto';
                    });
                } else {
                    // Input biasa
                    const field = this.form.elements[fieldName];
                    if (field) {
                        field.disabled = false;
                        field.style.opacity = '1';
                        field.style.pointerEvents = 'auto';
                    }
                }
            }

            /**
             * Disable semua field kecuali field pertama
             */
            disableAllFieldsExceptFirst() {
                const allInputs = this.form.querySelectorAll('input:not([name="nim"]), select, textarea');
                allInputs.forEach(input => {
                    input.disabled = true;
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        input.parentElement.style.opacity = '0.5';
                        input.parentElement.style.pointerEvents = 'none';
                    } else {
                        input.style.opacity = '0.5';
                        input.style.pointerEvents = 'none';
                    }
                });

                // Disable tombol submit
                const submitBtn = this.form.querySelector('input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.pointerEvents = 'none';
                }
            }

            /**
             * Enable tombol submit
             */
            enableSubmitButton() {
                const submitBtn = this.form.querySelector('input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.pointerEvents = 'auto';
                }
            }

            /**
             * Validasi seluruh form sebelum submit
             */
            validateForm() {
                // Validasi semua field text
                const textFields = ['nim', 'nama', 'tempat_lahir', 'tanggal_lahir', 
                                   'alamat', 'kota', 'email', 'no_hp', 'umur'];
                
                for (let fieldName of textFields) {
                    if (!this.validateField(fieldName, true)) {
                        return false;
                    }
                }

                // Validasi radio button
                if (!this.validateRadio('jk')) {
                    alert('Silakan pilih jenis kelamin');
                    return false;
                }

                if (!this.validateRadio('status')) {
                    alert('Silakan pilih status');
                    return false;
                }

                // Validasi checkbox
                if (!this.validateCheckbox('hobi[]')) {
                    alert('Silakan pilih minimal 1 hobi');
                    return false;
                }

                return true;
            }
        }

        // Instance global validator
        let validator;

        /**
         * Handler untuk navigasi dengan Enter key
         */
        function handleEnterKey(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                
                const form = document.forms["formMahasiswa"];
                const elements = Array.from(form.elements).filter(el => {
                    return !el.disabled && 
                           ((el.tagName === 'INPUT' && 
                             el.type !== 'radio' && 
                             el.type !== 'checkbox' && 
                             el.type !== 'submit' && 
                             el.type !== 'reset') ||
                            el.tagName === 'SELECT' ||
                            el.tagName === 'TEXTAREA');
                });
                
                const currentIndex = elements.indexOf(event.target);
                
                if (currentIndex > -1 && currentIndex < elements.length - 1) {
                    elements[currentIndex + 1].focus();
                }
            }
        }

        /**
         * Handler untuk validasi field umum
         */
        function onFieldBlur(fieldName) {
            if (validator.validateField(fieldName, true)) {
                validator.enableNextField(fieldName);
            }
        }

        /**
         * Handler untuk validasi radio button
         */
        function onRadioChange(fieldName) {
            if (validator.validateRadio(fieldName)) {
                validator.enableNextField(fieldName);
            }
        }

        /**
         * Handler untuk validasi checkbox
         */
        function onCheckboxChange() {
            if (validator.validateCheckbox('hobi[]')) {
                validator.enableSubmitButton();
            }
        }

        /**
         * Handler untuk submit form
         */
        function onFormSubmit() {
            return validator.validateForm();
        }

        /**
         * Inisialisasi saat halaman dimuat
         */
        window.onload = function() {
            // Buat instance validator
            validator = new FormValidator('formMahasiswa');
            
            // Disable semua field kecuali NIM
            validator.disableAllFieldsExceptFirst();
            
            // Focus ke field pertama
            validator.form.elements['nim'].focus();
        };
    </script>
</head>
<body>

<div class="container">
    <h2>Form Input Data Mahasiswa - POST</h2>
    <form name="formMahasiswa" action="proses_post_sanitasi.php" method="POST" onsubmit="return onFormSubmit()">
        
        <div class="form-group">
            <label>NIM :</label> 
            <input type="text" 
                   name="nim" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('nim')">
            <small class="form-hint">Hanya angka</small>
        </div>

        <div class="form-group">
            <label>Nama :</label> 
            <input type="text" 
                   name="nama" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('nama')">
            <small class="form-hint">Tidak boleh kosong dan tidak boleh mengandung angka</small>
        </div>

        <div class="form-group">
            <label>Tempat Lahir :</label> 
            <input type="text" 
                   name="tempat_lahir" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('tempat_lahir')">
            <small class="form-hint">Hanya huruf</small>
        </div>

        <div class="form-group">
            <label>Tanggal Lahir :</label> 
            <input type="date" 
                   name="tanggal_lahir" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onchange="onFieldBlur('tanggal_lahir')">
        </div>

        <div class="form-group">
            <label>Alamat :</label> 
            <textarea name="alamat" 
                      rows="4" 
                      class="form-control" 
                      required 
                      onkeydown="handleEnterKey(event)" 
                      onblur="onFieldBlur('alamat')"></textarea>
            <small class="form-hint">Minimal 10 karakter</small>
        </div>

        <div class="form-group">
            <label>Kota :</label>
            <select name="kota" 
                    class="form-control" 
                    required 
                    onkeydown="handleEnterKey(event)" 
                    onchange="onFieldBlur('kota')">
                <option value="">-- Pilih Kota --</option>
                <option>Semarang</option>
                <option>Solo</option>
                <option>Salatiga</option>
                <option>Kudus</option>
                <option>Pekalongan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Jenis Kelamin :</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" 
                           name="jk" 
                           value="Laki-laki" 
                           required 
                           onchange="onRadioChange('jk')"> Laki-laki
                </label>
                <label class="radio-label">
                    <input type="radio" 
                           name="jk" 
                           value="Perempuan" 
                           required 
                           onchange="onRadioChange('jk')"> Perempuan
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Email :</label> 
            <input type="email" 
                   name="email" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('email')">
            <small class="form-hint">Format: nama@domain.com</small>
        </div>

        <div class="form-group">
            <label>No HP :</label> 
            <input type="text" 
                   name="no_hp" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('no_hp')">
            <small class="form-hint">10-15 digit angka</small>
        </div>

        <div class="form-group">
            <label>Umur :</label> 
            <input type="text" 
                   name="umur" 
                   class="form-control" 
                   required 
                   onkeydown="handleEnterKey(event)" 
                   onblur="onFieldBlur('umur')">
            <small class="form-hint">Tidak boleh kosong dan tidak boleh mengandung huruf (17-100 tahun)</small>
        </div>

        <div class="form-group">
            <label>Status :</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" 
                           name="status" 
                           value="Kawin" 
                           required 
                           onchange="onRadioChange('status')"> Kawin
                </label>
                <label class="radio-label">
                    <input type="radio" 
                           name="status" 
                           value="Belum Kawin" 
                           required 
                           onchange="onRadioChange('status')"> Belum Kawin
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Hobi :</label>
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" 
                           name="hobi[]" 
                           value="Membaca" 
                           onchange="onCheckboxChange()"> Membaca
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" 
                           name="hobi[]" 
                           value="Olah Raga" 
                           onchange="onCheckboxChange()"> Olah Raga
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" 
                           name="hobi[]" 
                           value="Musik" 
                           onchange="onCheckboxChange()"> Musik
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" 
                           name="hobi[]" 
                           value="Traveling" 
                           onchange="onCheckboxChange()"> Traveling
                </label>
            </div>
            <small class="form-hint">Pilih minimal 1 hobi</small>
        </div>

        <div class="form-actions">
            <input type="submit" value="Kirim" class="btn btn-primary">
            <input type="reset" value="Reset" class="btn btn-secondary">
        </div>
    </form>
</div>

</body>
</html>
