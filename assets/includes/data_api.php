<?php 
require_once 'init.php';

function data($data){
    echo '{"result": true, "data": {"contents":'. json_encode($data).',"pagination": {
        "page": 1,
        "totalCount": 100
      }}}';
}

if(isset($_GET['sales'])){
    echo '[150, 41, 2354, 51, 49, 1520, 69, 91, 3520,0,0,0]';
    // var_dump($_GET);
}


if(isset($_GET['all_company_info'])){
    $data = array();

    $all_companys = $database->con->query('SELECT * FROM company')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_companys as $company){
        $company_entry = array();
        $company_entry['id'] = $company->id;
        $company_entry['name'] = $company->name;
        $company_entry['created'] = $company->created_at;
        $company_entry['address'] = $company->address;
        $company_entry['city'] = $company->city;
        $company_entry['country'] = $company->country;
        $company_entry['image'] = $company->image;
        //get all sub contacts count
        $contacts_count = $database->con->query("SELECT count(*) FROM contacts WHERE company_id = $company->id")->fetchColumn();
        $company_entry['contacts_count'] = $contacts_count;
        //get all sub files count 
        $files_count = $database->con->query("SELECT count(*) FROM files WHERE company_id = $company->id")->fetchColumn();
        $company_entry['files_count'] = $files_count;
        $data[] = $company_entry;
    }

    data($data);
}

if(isset($_GET['all_contacts_info'])){
    $data = array();

    $all_contacts = $database->con->query('SELECT * FROM contacts')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_contacts as $contact){
        $contact_entry = array();
        $contact_entry['id'] = $contact->id;
        $contact_entry['name'] = $contact->name;
        $contact_entry['created'] = $contact->created_at;
        $contact_entry['address'] = $contact->address;
        $contact_entry['city'] = $contact->city;
        $contact_entry['country'] = $contact->country;
        $contact_entry['email'] = $contact->email;
        $contact_entry['currency'] = $contact->currency;
        $contact_entry['type'] = $contact->type;
        //get all sub files count 
        $files_count = $database->con->query("SELECT count(*) FROM files WHERE contact_id = $contact->id")->fetchColumn();
        $contact_entry['files_count'] = $files_count;
        $data[] = $contact_entry;
    }
    data($data);
}
if(isset($_GET['all_items'])){
    $data = array();

    $all_items = $database->con->query('SELECT * FROM items')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_items as $item){
        $contact_item = array();
        $contact_item['id'] = $item->id;
        $contact_item['name'] = $item->name;
        $contact_item['created'] = $item->created_at;
        $contact_item['shot_name'] = $item->shot_name;
        $contact_item['ean'] = $item->ean;
        $contact_item['description'] = $item->description;
        $contact_item['color'] = $item->color;
        $contact_item['brand'] = $item->brand;
        $contact_item['pn'] = $item->pn;
        //get stock 
        $inventory_items = $database->con->query("SELECT SUM(qty_remaning) AS total_qty_remaning FROM inventory_item WHERE item = $item->id")->fetch(PDO::FETCH_OBJ);
        // $inventory_count = 0;
        $contact_item['current_inventory'] = $inventory_items->total_qty_remaning | 0;
        $data[] = $contact_item;
    }
    data($data);
}

if(isset($_GET['all_vendors'])){
    $data = array();
    $all_vendors = $database->con->query('SELECT * FROM vendors LEFT JOIN contacts ON vendors.contact=contacts.id')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_vendors as $vendor){
        $data[] = $vendor;
    }
    data($data);
}

if(isset($_GET['all_customers'])){
    $data = array();
    $all_vendors = $database->con->query('SELECT * FROM customers LEFT JOIN contacts ON customers.contact=contacts.id')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_vendors as $vendor){
        $data[] = $vendor;
    }
    data($data);
}
if(isset($_GET['all_forworders'])){
    $data = array();
    $all_vendors = $database->con->query('SELECT * FROM forworders LEFT JOIN contacts ON forworders.contact=contacts.id')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_vendors as $vendor){
        $data[] = $vendor;
    }
    data($data);
}
if(isset($_GET['all_files'])){
    $data = array();
    //TODO: expand the purchase sale and other id to the actual deal number
    // W3Scools exsample:
    // SELECT Orders.*, Customers.CustomerName as somthing, Shippers.ShipperName
    // FROM ((Orders
    // INNER JOIN Customers ON Orders.CustomerID = Customers.CustomerID)
    // INNER JOIN Shippers ON Orders.ShipperID = Shippers.ShipperID);
    //TODO: add files number to the db as a

    $all_files = $database->con->query('SELECT files.*, contacts.name as contact FROM files INNER JOIN contacts ON files.contact_id=contacts.id')->fetchall(PDO::FETCH_OBJ);
    foreach ($all_files as $file){
        $file->deal = $file->purchase_id | $file->sale_id | $file->freight_id | $file->other_id;
        // $contact = $database->con->query("SELECT * FROM contacts WHERE id = $file->contact_id limit 1")->fetch(PDO::FETCH_OBJ);
        // $file->contact = $contact->name;
        // $file->deal = 
        $data[] = $file;
    }
    data($data);
}
if(isset($_GET['files_c_id'])){
    $contact = $_GET['files_c_id'];
    $data = array();
    $all_files = $database->con->prepare('SELECT * FROM files WHERE contact_id = ?');
    $all_files->bindParam(1,$contact);
    $all_files->execute();
    $all_files = $all_files->fetchall(PDO::FETCH_OBJ);
    foreach ($all_files as $file){
        $data[] = $file;
    }
    data($data);
}