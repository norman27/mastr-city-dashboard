use clap::Parser;

use crate::client::soap_client::{ports::AllgemeineFunktionen, messages::GetLokaleUhrzeitRequest, types::{GetLokaleUhrzeitParameter, GetAktuellerStandTageskontingentParameter}};

mod client;

#[derive(Parser, Debug)]
#[command(author, version, about, long_about = None)]
struct Args {
    /// City name
    #[arg(short, long)]
    city: String,
}

//@TODO try zeep: https://github.com/mibes404/zeep/tree/7ce0004d2bcf9b97e0dac2aaf2fc813920a34675

#[tokio::main]
async fn main() {
    let args = Args::parse();
    let city = &args.city;
    println!("Hello, world! {}", city);

    // get soap client
    let soap_user = "TODO".to_string();
    let soap_pass = "TODO".to_string();
    let client = client::soap_client::services::Marktstammdatenregister::new_client(Some((soap_user, soap_pass)));
    
    //let response = client.get_lokale_uhrzeit(GetLokaleUhrzeitRequest { parameters_i: GetLokaleUhrzeitParameter {} });
    let response = client.get_aktueller_stand_tageskontingent(client::soap_client::messages::GetAktuellerStandTageskontingentRequest { parameters_i: (GetAktuellerStandTageskontingentParameter { authentifizierte_anfrage_mit_marktakteur_basis: todo!(), xsi_type: todo!() }) });
    
    // Failed to unmarshal SOAP response: "bad namespace for GetLokaleUhrzeitResponse, found https://www.marktstammdatenregister.de/Services/Public/1_2/Modelle/Common"
    println!("{:?}", response.await);
}
