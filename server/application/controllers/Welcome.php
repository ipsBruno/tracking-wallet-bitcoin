<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function index($id)
	{
		
		log_message('error', sprintf("get[%s]", $id));

		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$url = "https://blockchain.info/pt/block-height/" . $id . "?format=json";

		$json = null;

		try {
			$json = json_decode(file_get_contents($url), true);
		} catch (Exception $e) {
			return $this->index($id);
		}


		if (!isset($json["blocks"][0]["tx"][0])) {
			return true;
		}

		$transactions = $json["blocks"][0]["tx"];


		foreach ($transactions as $transaction) {
			if (!isset($transaction["inputs"])) {
				continue;
			}

			$inputs = [];

			foreach ($transaction["inputs"] as $input) {
				if (!isset($input["prev_out"]["addr"])) {
					continue;
				}
				array_push($inputs, $input["prev_out"]["addr"]);
			}

			if (!count($inputs)) {
				continue;
			}

			$inputs = array_unique($inputs);

			$input_wallet = null;



			$inputs_in_db = $this->db->where_in('addr', $inputs)->get('adresses')->result_array();
			$queryUpdate= null;
			$queryInsert = null;
			if ($inputs_in_db && count($inputs_in_db) > 0) {
				$wallet_in_db = array_column($inputs_in_db, "wallet");
				$addr_in_db = array_column($inputs_in_db, "addr");

				foreach ($inputs as $k=>$input) {
					foreach ($addr_in_db as $addr_db) {
						if ($addr_db === $input) {
							unset($inputs[$k]);
						}
					}
				}

				if (!count($inputs)) {
					continue;
				}

				$input_wallet =  $inputs_in_db[0]["wallet"];
				$queryUpdate = "update adresses set wallet=".$input_wallet." where wallet in (".implode(",", $wallet_in_db).") ; ";

				if (isset($wallet_in_db[1])) {
					unset($wallet_in_db[0]);
					$this->db->where_in("id", $wallet_in_db)->delete("wallets");
				}
			} else {
				$this->db->insert('wallets', ["id"=>null]);
				$input_wallet = $this->db->insert_id();
			}
			$inputs_insert = [];
			foreach ($inputs as $input) {
				$inputs_insert[] = "('".$input."'".",".$input_wallet.")";
			}
			$queryInsert = "insert into adresses (addr,wallet) VALUES " . implode(",", $inputs_insert) . " on duplicate key update wallet=" . $input_wallet;
			$this->db->trans_start();
			if ($queryUpdate != null) {
				$this->db->query($queryUpdate);
			}
			if ($queryInsert != null) {
				$this->db->query($queryInsert);
			}
			$this->db->trans_complete();
		}

		return true;
	}
}
