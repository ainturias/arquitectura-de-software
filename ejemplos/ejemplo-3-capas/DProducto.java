package Datos;

import Database.Conexion;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class DProducto {
    int id;
    String nombre;
    Double precio;
    int idCategoria;
    Connection con;
    PreparedStatement ps;
    ResultSet rs;

    public DProducto() {
        Conexion conectar = new Conexion();
        con = conectar.getConnection();
    }

    public DProducto(int id, String nombre, Double precio) {
        this.id = id;
        this.nombre = nombre;
        this.precio = precio;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public Double getPrecio() {
        return precio;
    }

    public void setPrecio(Double precio) {
        this.precio = precio;
    }
    
    public int getIdCategoria() {
        return idCategoria;
    }

    public void setIdCategoria(int idCategoria) {
        this.idCategoria = idCategoria;
    }
    
    public DefaultTableModel getProductos() throws SQLException {
        String query = "select * from producto";
        ps = con.prepareStatement(query);
        rs = ps.executeQuery();
        DefaultTableModel table = new DefaultTableModel();
        try {        
            table.setColumnIdentifiers(new String[]{"id", "nombre", "precio", "idCategoria"});
            Object[] tableData = new Object[table.getColumnCount()];
            while (rs.next()) {
                tableData[0] = rs.getInt("id");
                tableData[1] = rs.getString("nombre");
                tableData[2] = rs.getDouble("precio");
                tableData[3] = rs.getInt("idCategoria");
                table.addRow(tableData);
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al listar " + e, "Error", JOptionPane.ERROR_MESSAGE);
            return null;
        }
        return table;
    }
    
    public ArrayList getProductos2() throws SQLException{  
        String query = "select * from producto";
        ps = con.prepareStatement(query);
        rs = ps.executeQuery();
        ArrayList<Object[]> productos = new ArrayList<>();
        try {
            
            while(rs.next()){
                productos.add(new Object[]{rs.getInt(1),rs.getString(2), rs.getDouble(3), rs.getInt(4)});
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al listar "+e,"Error",JOptionPane.ERROR_MESSAGE);
            return null;
        }
        return productos;
    }
    
    public boolean crear() {
        String query = "insert into producto (nombre, precio, idCategoria) values (?, ?, ?)";
        try {
            ps = con.prepareStatement(query);
            ps.setString(1, this.nombre);
            ps.setDouble(2, this.precio);
            ps.setInt(3, this.idCategoria);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al insertar el producto en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    public boolean editar() {
        String query = "update producto set nombre = ?, precio = ?, idCategoria = ? where id = ?";
        try {
            ps = con.prepareStatement(query);
            ps.setString(1, this.nombre);
            ps.setDouble(2, this.precio);
            ps.setInt(3, this.idCategoria);
            ps.setInt(4, this.id);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al editar el producto en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    public boolean eliminar() {
        String query = "delete from producto where id = ? ";
        try {
            PreparedStatement ps = con.prepareStatement(query);
            ps.setInt(1, this.id);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al eliminar el producto de la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    
}
