# Usage Example : 
$datatables = Database::connect()->table('pengguna_grup');
		$datatables->select('pengguna_grup_nama, pengguna_grup_id');
		$datatables->where('pengguna_jenis_id', '2');
		$datatables->where('status', '1');
		$datatables = new Datatables($datatables);
		//Harus ada Spasi
		$datatables->search('pengguna_grup_nama, pengguna_grup_id');

		$data = $datatables->getData();
		foreach ($data as $key => $value) {
			$button = '';
			$url = base_url("administrator/grup/form?i=$value[pengguna_grup_id]");
			if ($cek->update != 0) {
				$button .= "<a href=\"$url\" class=\"btn btn-sm btn-warning mr-2\"><i class=\"fa fa-edit\"></i></a>";
			}
			if ($cek->delete != 0) {
				$button .= "<button onclick=\"hapus($value[pengguna_grup_id])\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-trash\"></i></button>";
			}
			$data[$key]['pengguna_grup_id'] = $button;
		}
		$datatables->renderNoKeys($data);
