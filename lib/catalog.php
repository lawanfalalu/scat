<?php

class Brand extends Model {
  public function products() {
    return $this->has_many('Product');
  }
}

class Department extends Model {
  public function parent() {
    return $this->belongs_to('Department', 'parent_id');
  }

  public function departments() {
    return $this->has_many('Department', 'parent_id');
  }

  public function products() {
    return $this->has_many('Product');
  }
}

class Product extends Model {
  public function brand() {
    return $this->belongs_to('Brand');
  }

  public function items() {
    return $this->has_many('Item');
  }
}

class Item extends Model {
  /* XXX Legacy, should get from parent product */
  public function brand() {
    return $this->belongs_to('Brand', 'brand');
  }

  public function product() {
    return $this->belongs_to('Product');
  }

  public function barcodes() {
    return $this->has_many('Barcode', 'item');
  }
}

class Barcode extends Model {
  public function item() {
    return $this->belongs_to('Item', 'item');
  }
}
