package org.forcepower.starcement.activity.test;

import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;

import java.util.ArrayList;

public class TextActivity extends AppCompatActivity {
    RecyclerView firstListView=null;
    ArrayList<Main> dataItemList = new ArrayList<>();
    MainAdapter adapter = new MainAdapter(this, dataItemList);
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_text);

        init();
    }
    private void init (){
        firstListView=findViewById(R.id.firstListView);
        for(var i=0;i<5;i++){
            Main objMain=new Main();
            objMain.setObj(new ArrayList<>());
            ArrayList<Child> dataChild=new ArrayList<>();
            for(var j=0;j<2;j++){
                Child objChild=new Child();
                ArrayList<SubChild> dataSubChild=new ArrayList<>();
                for(var k=0;k<4;k++){
                    SubChild objSubChild=new SubChild();

                    objSubChild.setTitle("Sub Child "+(i+1)+" "+(j+1)+" "+(k+1));

                    dataSubChild.add(objSubChild);
                }

                objChild.setObj(dataSubChild);
                objChild.setData("Child "+(i+1)+" "+(j+1));
                objChild.setExpanded(false);

                dataChild.add(objChild);
            }
            objMain.setObj(dataChild);
            objMain.setDataSet("main "+(i+1));
            objMain.setExpanded(false);
            dataItemList.add(objMain);
        }

        showInData();
    }

    private void showInData(){
        firstListView.setLayoutManager(new LinearLayoutManager(this));
        firstListView.setAdapter(adapter);
        adapter.notifyDataSetChanged();
    }
}

class Main{
    String dataSet;
    ArrayList<Child> obj= new ArrayList<>();
    boolean isExpanded = false;

    public String getDataSet() {
        return dataSet;
    }

    public void setDataSet(String dataSet) {
        this.dataSet = dataSet;
    }

    public ArrayList<Child> getObj() {
        return obj;
    }

    public void setObj(ArrayList<Child> obj) {
        this.obj = obj;
    }

    public boolean isExpanded() {
        return isExpanded;
    }

    public void setExpanded(boolean expanded) {
        isExpanded = expanded;
    }
}

class Child{
    String data;
    ArrayList<SubChild> obj = new ArrayList<>();
    boolean isExpanded = false;

    public String getData() {
        return data;
    }

    public void setData(String data) {
        this.data = data;
    }

    public  ArrayList<SubChild> getObj() {
        return obj;
    }

    public void setObj( ArrayList<SubChild> obj) {
        this.obj = obj;
    }

    public boolean isExpanded() {
        return isExpanded;
    }

    public void setExpanded(boolean expanded) {
        isExpanded = expanded;
    }
}

class SubChild{
    String title;

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }
}