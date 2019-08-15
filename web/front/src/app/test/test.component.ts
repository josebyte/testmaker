import { Component, OnInit, ViewChildren} from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators} from "@angular/forms";
import { MatStepper} from "@angular/material";

@Component({
  selector: 'app-test',
  templateUrl: './test.component.html',
  styleUrls: ['./test.component.scss']
})
export class TestComponent implements OnInit {
  @ViewChildren('stepper') stepper: any;

  formGroups: Array<FormGroup> = [];
  form: FormGroup;

  isLoading = false;

  questions = [
    {id: 1, question: "Question 1", answers: [{id: 1, answer: "Answer a", correct: false}, {id: 1, answer: "Answer b", correct: false}, {id: 1, answer: "Answer c", correct: true}, {id: 1, answer: "Answer d", correct: false}]},
    {id: 2, question: "Question 2", answers: [{id: 1, answer: "Answer a", correct: false}, {id: 1, answer: "Answer b", correct: false}, {id: 1, answer: "Answer c", correct: false}, {id: 1, answer: "Answer d", correct: true}]},
  ];

  constructor(private fb: FormBuilder) { }

  ngOnInit() {
    for (let i = 0; i < this.questions.length; i++) {
      const group = this.fb.group({});
      group.addControl(''+i, new FormControl(i, Validators.required));
      this.formGroups.push(group);
    }

    this.isLoading = true;
  }

  moveNext(stepper: MatStepper){
    stepper.next();
  }

}
