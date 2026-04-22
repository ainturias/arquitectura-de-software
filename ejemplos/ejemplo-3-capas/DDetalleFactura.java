package Datos;

import Database.Conexion;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class DDetalleFactura {
    int id;
    int idFactura;
    int idProducto;
    double precio;
    int cantidad;
    Connection con;
    PreparedStatement ps;
    ResultSet rs;

    public DDetalleFactura() {
        Conexion conectar = new Conexion();
        con = conectar.getConnection();
    }

    public DDetalleFactura(int id, int idFactura, int idProducto, double precio, int cantidad) {
        this.id = id;
        this.idFactura = idFactura;
        this.idProducto = idProducto;
        this.precio = precio;
        this.cantidad = cantidad;
    }
    
    public DDetalleFactura(int idFactura, int idProducto, double precio, int cantidad) {
        this.idFactura = idFactura;
        this.idProducto = idProducto;
        this.precio = precio;
        this.cantidad = cantidad;
        Conexion conectar = new Conexion();
        con = conectar.getConnection();
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIdFactura() {
        return idFactura;
    }

    public void setIdFactura(int idFactura) {
        this.idFactura = idFactura;
    }

    public int getIdProducto() {
        return idProducto;
    }

    public void setIdProducto(int idProducto) {
        this.idProducto = idProducto;
    }

    public Double getPrecio() {
        return precio;
    }

    public void setPrecio(double precio) {
        this.precio = precio;
    }

    public int getCantidad() {
        return cantidad;
    }

    public void setCantidad(int cantidad) {
        this.cantidad = cantidad;
    }
      
    
    public boolean crearDetalleFactura() {
        String query = "insert into detallefactura (idFactura, idProducto, precio, cantidad) values (?, ?, ?, ?)";
        try {
            ps = con.prepareStatement(query);
            ps.setInt(1, this.idFactura);
            ps.setInt(2, this.idProducto);
            ps.setDouble(3, this.precio);
            ps.setInt(4, this.cantidad);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al insertar la factura en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }    
    
    
}
