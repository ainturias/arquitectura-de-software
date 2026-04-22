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
public class DCategoria {
    int id;
    String nombre;
    Connection con;
    PreparedStatement ps;
    ResultSet rs;

    public DCategoria() {
        Conexion conectar = new Conexion();
        con = conectar.getConnection();
    }

    public DCategoria(int id, String nombre) {
        this.id = id;
        this.nombre = nombre;
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
    
    public DefaultTableModel getCategorias() throws SQLException {
        String query = "select * from categoria";
        PreparedStatement ps = con.prepareStatement(query);
        ResultSet rs = ps.executeQuery();
        DefaultTableModel table = new DefaultTableModel();
        try {        
            table.setColumnIdentifiers(new String[]{"id", "nombre"});
            Object[] tableData = new Object[table.getColumnCount()];
            while (rs.next()) {
                tableData[0] = rs.getInt("id");
                tableData[1] = rs.getString("nombre");
                table.addRow(tableData);
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al listar " + e, "Error", JOptionPane.ERROR_MESSAGE);
            return null;
        }
        return table;
    }
    
    public ArrayList getCategorias2() throws SQLException{  
        String query = "select * from categoria";
        ps = con.prepareStatement(query);
        rs = ps.executeQuery();
        ArrayList<Object[]> categorias = new ArrayList<>();
        try {
            
            while(rs.next()){
                categorias.add(new Object[]{rs.getInt(1),rs.getString(2)});
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al listar "+e,"Error",JOptionPane.ERROR_MESSAGE);
            return null;
        }
        return categorias;
    }
     
    public boolean crear() {
        String query = "insert into categoria (nombre) values (?)";
        try {
            PreparedStatement ps = con.prepareStatement(query);
            ps.setString(1, this.nombre);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al insertar categoria en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    public boolean editar() {
        String query = "update categoria set nombre = ? where id = ? ";
        try {
            PreparedStatement ps = con.prepareStatement(query);
            ps.setString(1, this.nombre);
            ps.setInt(2, this.id);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al editar la categoria en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    public boolean eliminar() {
        String query = "delete from categoria where id = ? ";
        try {
            PreparedStatement ps = con.prepareStatement(query);
            ps.setInt(1, this.id);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al eliminar la categoria de la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
}
