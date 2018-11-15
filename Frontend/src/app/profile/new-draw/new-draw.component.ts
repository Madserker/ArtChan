import { Component, OnInit, Input } from '@angular/core';
import { ListsService } from '../../lists.service';
import { NgForm } from '@angular/forms';
import { User } from '../../_models/User.interface';


@Component({
  selector: 'app-new-draw',
  templateUrl: './new-draw.component.html',
  styleUrls: ['./new-draw.component.less']
})
export class NewDrawComponent implements OnInit {

  file : File
  @Input() currentUser : User

  constructor(private lists : ListsService) { }

  ngOnInit() {




  }




  // Get the modal


// // Get the button that opens the modal
// btn = document.getElementById("myBtn");

// // Get the <span> element that closes the modal
// var span = document.getElementsByClassName("close")[0];

// // When the user clicks the button, open the modal 
openForm(){
    document.getElementById('myModal1').style.display = "block"
}
closeForm(){
  document.getElementById('myModal1').style.display = "none"
}


// // When the user clicks anywhere outside of the modal, close it
// window.onclick = function(event) {
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
// }


fileChange(event) {
  let fileList: FileList = event.target.files;
  if(fileList.length > 0) {
      this.file = fileList[0];
  }
}

uploadDraw(form: NgForm){
  console.log(this.file)
  this.lists.uploadDraw(
    form.value.name,
    form.value.desc,
    this.file,
    this.currentUser.username
    ).subscribe(
      response =>  window.location.reload(),//si ha ido bien el login
      error => console.log(error)//si no ha ido bien el login
    );
}


}




