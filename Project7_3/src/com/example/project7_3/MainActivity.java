package com.example.project7_3;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.text.AlteredCharSequence;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class MainActivity extends Activity {
	
	TextView tvName, tvEmail;
	Button button1;
	EditText dlgEdtName, dlgEdtEmail;
	TextView toastText;
	View dialogView, toastView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		setTitle("����� ���� �Է�");
		
		tvName = (TextView) findViewById(R.id.tvName);
		tvEmail = (TextView) findViewById(R.id.tvEmail);
		button1 = (Button) findViewById(R.id.button1);
		
		button1.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				dialogView = (View) View.inflate(MainActivity.this, R.layout.dialog1, null);
				AlertDialog.Builder dlg = new AlertDialog.Builder(MainActivity.this);
				dlg.setTitle("����� ���� �Է�");
				dlg.setIcon(R.drawable.ic_menu_allfriends);
				dlg.setView(dialogView);
				dlg.setPositiveButton("Ȯ��", 
						new DialogInterface.OnClickListener() {
							
							@Override
							public void onClick(DialogInterface dialog, int which) {
								// TODO Auto-generated method stub
								dlgEdtName = (EditText) dialogView.findViewById(R.id.dlgEdt1);
								dlgEdtEmail = (EditText) dialogView.findViewById(R.id.dlgEdt2);
								
								tvName.setText(dlgEdtName.getText().toString());
								tvEmail.setText(dlgEdtEmail.getText().toString());
							}
						});
				dlg.setNegativeButton("���", null);
				dlg.show();
			}
		});
	}
	

}
