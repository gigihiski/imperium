<?php
/**

*/

namespace Edoceo\Imperium;

use Radix;

if (empty($_GET['c'])) {
	return(0);
}

$c = new Contact(intval($_GET['c']));
if (empty($c['id'])) {
	Radix\Session::flash('fail', 'Contact not found');
	// radix::redirect('/contact');
}
$_ENV['contact'] = $c;

$this->Contact = $c;
// Why Pointing this way?
$this->Account = $c->getAccount();

$this->ContactList = array();

if (empty($c->parent_id)) {
	// $this->ContactList = Radix\DB\SQL::fetch_all("SELECT * FROM contact WHERE id != ? AND (parent_id = ? OR company = ?)",array($c->id,$c->id,$c->company));
	$this->ContactList = Radix\DB\SQL::fetch_all('SELECT * FROM contact WHERE id != ? AND parent_id = ?', array($c->id,$c->id));
}
$this->ContactAddressList = $c->getAddressList();
$this->ContactChannelList = $c->getChannelList();
$this->ContactNoteList = $c->getNotes();
$this->ContactFileList = $c->getFiles();
// @note what does order by star, status do? Join base_enum?
$this->WorkOrderList = Radix\DB\SQL::fetch_all('SELECT workorder.*, contact.name AS contact_name FROM workorder JOIN contact ON workorder.contact_id = contact.id WHERE workorder.contact_id = ? ORDER BY workorder.date DESC, workorder.id DESC', array($c['id']));
$this->InvoiceList = Radix\DB\SQL::fetch_all('SELECT * FROM invoice WHERE contact_id = ? ORDER BY date DESC, id DESC', array($c['id']));

$_ENV['title'] = array(
	$this->Contact['kind'],
	sprintf('#%d:%s', $this->Contact['id'], $this->Contact['name'])
);
