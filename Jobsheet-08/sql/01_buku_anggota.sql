CREATE TABLE IF NOT EXISTS buku (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    tahun INT,
    isbn VARCHAR(20),
    stok INT DEFAULT 0,
    kategori VARCHAR(50),
    penerbit VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_anggota VARCHAR(50) NOT NULL UNIQUE,
    alamat TEXT,
    no_hp VARCHAR(20)
);