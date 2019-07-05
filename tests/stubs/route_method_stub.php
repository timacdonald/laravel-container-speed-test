    public function {method_name}()
    {
        $response = $this->{http_method}("{http_path}");
        $response->assertStatus(200);
        $response->assertSee("OK");
    }

