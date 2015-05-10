package main

import (
	"database/sql"
	"fmt"
	_ "github.com/lib/pq"
	"log"
	"net/http"
	"strconv"
)

type temprecord struct {
	client     string
	ts_observe string
	val        float64
}

func response(w http.ResponseWriter, r *http.Request) {
	r.ParseForm()
	fmt.Println("'client:", r.FormValue("client"))
	fmt.Println("observe time:", r.FormValue("ts_observe"))
	fmt.Println("value: ", r.FormValue("val"))

	// check form parameter
	val, err := strconv.ParseFloat(r.FormValue("val"), 5)
	if err != nil {
		fmt.Println("Error value "+r.FormValue("val")+": ", err.Error())
		return
	}

	var temp temprecord
	temp.client = r.FormValue("client")
	temp.ts_observe = r.FormValue("ts_observe")
	temp.val = val
	id := insertdata(temp)
	if id > 0 {
		fmt.Fprintf(w, "Success")
	} else {
		fmt.Fprintf(w, "Fail")
	}
}

func insertdata(temp temprecord) int {
	db, err := sql.Open("postgres", "postgres://senssor:senssor@127.0.0.1/senssor?port=5489")
	if err != nil {
		fmt.Println("pq error:", err.Error())
	}
	var id int
	queryerr := db.QueryRow(`INSERT INTO se_temperature(client, val, ts_observe)
	VALUES('` + temp.client + `', ` + strconv.FormatFloat(temp.val, 'f', 6, 64) + `, '` + temp.ts_observe + `') RETURNING id`).Scan(&id)
	if queryerr != nil {
		fmt.Println("Query error: ", queryerr.Error())
	}

	return id
}

func main() {
	http.HandleFunc("/", response)
	err := http.ListenAndServe(":8009", nil)
	if err != nil {
		log.Fatal("Error: ", err)
	}
}
