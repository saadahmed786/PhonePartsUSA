<?php
class ModelUpsellOffer extends Model {
	public function getUpsellOffers($data = array()) {
		$sql = "SELECT uo.upsell_offer_id, uo.upsell_products, uo.cart_products, uod.title, uod.description FROM " . DB_PREFIX . "upsell_offer uo LEFT JOIN " . DB_PREFIX . "upsell_offer_description uod ON (uo.upsell_offer_id = uod.upsell_offer_id) WHERE uo.date_start <= NOW() AND if(uo.date_end != '0000-00-00', uo.date_end > NOW(), uo.date_end = '0000-00-00') AND stores LIKE '%^" . (int)$this->config->get('config_store_id') . "^%' AND uod.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['cart_total'])) {
			$sql .= " AND total_price_min <= '" . (float)$data['cart_total'] . "'";
		}
		
		if (!empty($data['cart_total'])) {
			$sql .= " AND if(uo.total_price_max != 0, uo.total_price_max >= '" . (float)$data['cart_total'] . "', uo.total_price_max = 0)";
		}
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
}
?>